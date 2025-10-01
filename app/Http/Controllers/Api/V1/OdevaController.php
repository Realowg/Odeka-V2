<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\OdevaService;
use App\Services\OdevaFunctionService;
use App\Services\OdevaContextService;
use App\Models\OdevaSubscription;
use App\Models\OdevaConversation;
use Illuminate\Http\Request;
use App\Http\Requests\Api\OdevaChatRequest;
use App\Http\Resources\OdevaChatResource;
use Carbon\Carbon;

class OdevaController extends BaseController
{
    protected $odevaService;
    protected $functionService;
    protected $contextService;

    public function __construct(
        OdevaService $odevaService,
        OdevaFunctionService $functionService,
        OdevaContextService $contextService
    ) {
        $this->odevaService = $odevaService;
        $this->functionService = $functionService;
        $this->contextService = $contextService;
    }

    /**
     * Chat with Odeva AI
     * 
     * @param OdevaChatRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function chat(OdevaChatRequest $request)
    {
        $creatorId = auth()->id();
        
        // Check if creator has active Odeva subscription
        $subscription = OdevaSubscription::where('creator_id', $creatorId)->first();
        
        if (!$subscription || !$subscription->isActive()) {
            return $this->forbiddenResponse('Odeva subscription required');
        }

        try {
            $response = $this->odevaService->chat(
                $request->message,
                $creatorId,
                $request->subscriber_id,
                $request->conversation_id
            );

            return $this->successResponse($response);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 500, 'ODEVA_ERROR');
        }
    }

    /**
     * Get available functions
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function functions()
    {
        $creatorId = auth()->id();
        $functions = $this->functionService->getFunctions($creatorId);

        return $this->successResponse([
            'functions' => $functions,
            'count' => count($functions),
        ]);
    }

    /**
     * Execute a function directly (for testing)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function executeFunction(Request $request)
    {
        $request->validate([
            'function_name' => 'required|string',
            'parameters' => 'required|array',
        ]);

        $creatorId = auth()->id();

        try {
            $result = $this->functionService->execute(
                $request->function_name,
                $request->parameters,
                $creatorId
            );

            return $this->successResponse($result);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400, 'FUNCTION_ERROR');
        }
    }

    /**
     * Get creator context
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContext()
    {
        $creator = auth()->user();
        $context = $this->contextService->getCreatorContext($creator);

        return $this->successResponse($context);
    }

    /**
     * Get automation status
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAutomation()
    {
        $subscription = OdevaSubscription::where('creator_id', auth()->id())->first();

        if (!$subscription) {
            return $this->notFoundResponse('Odeva subscription not found');
        }

        return $this->successResponse([
            'automation_enabled' => $subscription->automation_enabled,
            'settings' => $subscription->settings,
        ]);
    }

    /**
     * Update automation settings
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAutomation(Request $request)
    {
        $request->validate([
            'automation_enabled' => 'required|boolean',
            'settings' => 'sometimes|array',
        ]);

        $subscription = OdevaSubscription::where('creator_id', auth()->id())->first();

        if (!$subscription) {
            return $this->notFoundResponse('Odeva subscription not found');
        }

        $subscription->update([
            'automation_enabled' => $request->automation_enabled,
            'settings' => $request->settings ?? $subscription->settings,
        ]);

        return $this->successResponse([
            'automation_enabled' => $subscription->automation_enabled,
            'settings' => $subscription->settings,
        ], 'Automation settings updated');
    }

    /**
     * Get Odeva subscription status
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscription()
    {
        $subscription = OdevaSubscription::where('creator_id', auth()->id())->first();

        if (!$subscription) {
            return $this->successResponse([
                'has_subscription' => false,
                'message' => 'No Odeva subscription found',
            ]);
        }

        return $this->successResponse([
            'has_subscription' => true,
            'status' => $subscription->status,
            'is_active' => $subscription->isActive(),
            'is_on_trial' => $subscription->isOnTrial(),
            'trial_ends_at' => $subscription->trial_ends_at?->toDateString(),
            'next_billing_date' => $subscription->next_billing_date?->toDateString(),
            'price' => (float) $subscription->price,
            'currency' => $subscription->currency,
            'automation_enabled' => $subscription->automation_enabled,
        ]);
    }

    /**
     * Subscribe to Odeva
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(Request $request)
    {
        $creatorId = auth()->id();
        
        // Check if already subscribed
        $existing = OdevaSubscription::where('creator_id', $creatorId)->first();
        
        if ($existing && $existing->isActive()) {
            return $this->errorResponse('Already have an active Odeva subscription', null, 400, 'ALREADY_SUBSCRIBED');
        }

        // Create subscription with trial period
        $subscription = OdevaSubscription::create([
            'creator_id' => $creatorId,
            'status' => 'trial',
            'trial_ends_at' => Carbon::now()->addDays(14), // 14-day trial
            'price' => config('odeva.subscription_price', 29.99),
            'currency' => config('settings.currency_code', 'USD'),
            'automation_enabled' => false,
        ]);

        return $this->successResponse([
            'subscription_id' => $subscription->id,
            'status' => $subscription->status,
            'trial_ends_at' => $subscription->trial_ends_at->toDateString(),
            'message' => 'Odeva trial subscription activated',
        ], 'Subscription created successfully', 201);
    }

    /**
     * Cancel Odeva subscription
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelSubscription()
    {
        $subscription = OdevaSubscription::where('creator_id', auth()->id())->first();

        if (!$subscription) {
            return $this->notFoundResponse('Odeva subscription not found');
        }

        $subscription->update([
            'status' => 'cancelled',
            'automation_enabled' => false,
        ]);

        return $this->successResponse(null, 'Odeva subscription cancelled');
    }

    /**
     * Get Odeva analytics/usage stats
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analytics(Request $request)
    {
        $creatorId = auth()->id();
        $period = $request->get('period', 'month');

        // Get conversation count
        $conversationsQuery = OdevaConversation::where('creator_id', $creatorId);
        $conversationsQuery = $this->applyPeriodFilter($conversationsQuery, $period);
        $conversationsCount = $conversationsQuery->count();

        // Get total messages
        $totalMessages = OdevaConversation::where('creator_id', $creatorId)
            ->withCount('messages')
            ->get()
            ->sum('messages_count');

        return $this->successResponse([
            'period' => $period,
            'conversations' => $conversationsCount,
            'total_messages' => $totalMessages,
            'active_conversations' => OdevaConversation::where('creator_id', $creatorId)
                ->where('status', 'active')
                ->count(),
        ]);
    }

    /**
     * Apply period filter
     */
    protected function applyPeriodFilter($query, $period)
    {
        switch ($period) {
            case 'today':
                return $query->whereDate('created_at', Carbon::today());
            case 'week':
                return $query->where('created_at', '>=', Carbon::now()->subWeek());
            case 'month':
                return $query->where('created_at', '>=', Carbon::now()->subMonth());
            case 'year':
                return $query->where('created_at', '>=', Carbon::now()->subYear());
            default:
                return $query;
        }
    }
}
