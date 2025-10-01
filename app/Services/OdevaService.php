<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\OdevaConversation;
use App\Models\OdevaMessage;

class OdevaService
{
    protected $functionService;
    protected $contextService;
    
    public function __construct(
        OdevaFunctionService $functionService,
        OdevaContextService $contextService
    ) {
        $this->functionService = $functionService;
        $this->contextService = $contextService;
    }

    /**
     * Chat with Odeva AI
     *
     * @param string $message
     * @param int $creatorId
     * @param int|null $subscriberId
     * @param string|null $conversationId
     * @return array
     */
    public function chat(string $message, int $creatorId, int $subscriberId = null, string $conversationId = null)
    {
        $creator = User::find($creatorId);
        
        if (!$creator) {
            throw new \Exception('Creator not found');
        }

        // Get or create conversation
        $conversation = $this->getOrCreateConversation($creatorId, $subscriberId, $conversationId);

        // Build conversation history
        $messages = $this->buildConversationHistory($conversation);

        // Add user message
        $messages[] = [
            'role' => 'user',
            'content' => $message
        ];

        // Get system prompt with context
        $systemPrompt = $this->contextService->prepareSystemPrompt($creator);

        // Get available functions
        $tools = $this->functionService->getFunctions($creatorId);

        // Call Anthropic API
        $response = $this->callAnthropic($systemPrompt, $messages, $tools, $creatorId);

        // Save messages
        $this->saveMessage($conversation->id, 'user', $message);
        
        // Handle response (may include function calls)
        $assistantResponse = $this->handleResponse($response, $conversation, $creatorId);

        return [
            'conversation_id' => $conversation->id,
            'message' => $assistantResponse,
            'metadata' => [
                'model' => $response['model'] ?? 'claude-3-5-sonnet-20241022',
                'usage' => $response['usage'] ?? null,
            ]
        ];
    }

    /**
     * Call Anthropic API
     */
    protected function callAnthropic($systemPrompt, $messages, $tools, $creatorId)
    {
        $apiKey = config('services.anthropic.api_key');
        
        if (!$apiKey) {
            throw new \Exception('Anthropic API key not configured');
        }

        $payload = [
            'model' => 'claude-3-5-sonnet-20241022',
            'max_tokens' => 4096,
            'system' => $systemPrompt,
            'messages' => $messages,
        ];

        // Add tools if available
        if (!empty($tools)) {
            $payload['tools'] = $tools;
        }

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', $payload);

            if ($response->failed()) {
                Log::error('Anthropic API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Failed to communicate with Anthropic API');
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Anthropic API exception', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Handle API response (including function calls)
     */
    protected function handleResponse($response, $conversation, $creatorId)
    {
        $content = $response['content'] ?? [];
        $finalMessage = '';
        $functionResults = [];

        foreach ($content as $block) {
            if ($block['type'] === 'text') {
                $finalMessage .= $block['text'];
            } elseif ($block['type'] === 'tool_use') {
                // Execute function
                $functionName = $block['name'];
                $functionInput = $block['input'];
                
                try {
                    $result = $this->functionService->execute($functionName, $functionInput, $creatorId);
                    $functionResults[] = [
                        'function' => $functionName,
                        'result' => $result
                    ];

                    // If response requires continuation, make another API call
                    if ($response['stop_reason'] === 'tool_use') {
                        // Build follow-up messages with function results
                        $followUpMessages = $this->buildFollowUpMessages($response, $functionResults);
                        $systemPrompt = $this->contextService->prepareSystemPrompt(User::find($creatorId));
                        $tools = $this->functionService->getFunctions($creatorId);
                        
                        $followUpResponse = $this->callAnthropic($systemPrompt, $followUpMessages, $tools, $creatorId);
                        return $this->handleResponse($followUpResponse, $conversation, $creatorId);
                    }
                } catch (\Exception $e) {
                    Log::error('Function execution error', [
                        'function' => $functionName,
                        'error' => $e->getMessage()
                    ]);
                    $finalMessage .= "\n[Error executing function: {$functionName}]";
                }
            }
        }

        // Save assistant message
        $this->saveMessage($conversation->id, 'assistant', $finalMessage, $functionResults);

        return $finalMessage;
    }

    /**
     * Build follow-up messages with function results
     */
    protected function buildFollowUpMessages($previousResponse, $functionResults)
    {
        $messages = [];
        
        // Add previous assistant message
        $messages[] = [
            'role' => 'assistant',
            'content' => $previousResponse['content']
        ];

        // Add function results
        foreach ($functionResults as $result) {
            $messages[] = [
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'tool_result',
                        'tool_use_id' => $result['tool_use_id'] ?? uniqid(),
                        'content' => json_encode($result['result'])
                    ]
                ]
            ];
        }

        return $messages;
    }

    /**
     * Get or create conversation
     */
    protected function getOrCreateConversation($creatorId, $subscriberId, $conversationId)
    {
        if ($conversationId) {
            $conversation = OdevaConversation::find($conversationId);
            if ($conversation) {
                return $conversation;
            }
        }

        return OdevaConversation::create([
            'creator_id' => $creatorId,
            'subscriber_id' => $subscriberId,
            'status' => 'active',
        ]);
    }

    /**
     * Build conversation history
     */
    protected function buildConversationHistory($conversation)
    {
        $messages = OdevaMessage::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->take(50) // Last 50 messages for context
            ->get();

        return $messages->map(function ($msg) {
            return [
                'role' => $msg->role,
                'content' => $msg->content
            ];
        })->toArray();
    }

    /**
     * Save message
     */
    protected function saveMessage($conversationId, $role, $content, $metadata = null)
    {
        return OdevaMessage::create([
            'conversation_id' => $conversationId,
            'role' => $role,
            'content' => $content,
            'metadata' => $metadata ? json_encode($metadata) : null,
        ]);
    }

    /**
     * Get available functions for creator
     */
    public function getAvailableFunctions($creatorId)
    {
        return $this->functionService->getFunctions($creatorId);
    }
}

