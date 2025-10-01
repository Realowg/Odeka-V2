<?php

namespace App\Services;

use App\Models\User;
use App\Models\Subscriptions;
use App\Models\Updates;
use App\Models\OdevaExample;

class OdevaContextService
{
    /**
     * Prepare system prompt with creator context
     *
     * @param User $creator
     * @return string
     */
    public function prepareSystemPrompt(User $creator)
    {
        $context = $this->getCreatorContext($creator);
        $examples = $this->getCreatorExamples($creator->id);

        $prompt = "You are Odeva, an AI assistant for {$creator->name} (@{$creator->username}), a content creator on Odeka Media platform.\n\n";
        
        $prompt .= "## Creator Information:\n";
        $prompt .= "- Name: {$creator->name}\n";
        $prompt .= "- Username: @{$creator->username}\n";
        $prompt .= "- Bio: " . ($creator->story ?? 'N/A') . "\n";
        $prompt .= "- Total Subscribers: {$context['subscribers']}\n";
        $prompt .= "- Total Posts: {$context['posts']}\n";
        $prompt .= "- Account Balance: {$context['balance']} {$context['currency']}\n\n";

        $prompt .= "## Your Role:\n";
        $prompt .= "You assist {$creator->name} with managing their content creator account. You can:\n";
        $prompt .= "- Answer questions about earnings, subscribers, and analytics\n";
        $prompt .= "- Search and retrieve messages from subscribers\n";
        $prompt .= "- Send messages to subscribers (when explicitly requested)\n";
        $prompt .= "- Provide insights on content performance\n";
        $prompt .= "- Help manage the creator's workflow\n\n";

        if (!empty($examples)) {
            $prompt .= "## Creator's Communication Style:\n";
            $prompt .= "The creator has provided these examples of how they typically communicate with subscribers:\n\n";
            foreach ($examples as $example) {
                $prompt .= "Example {$example->id}:\n";
                $prompt .= "Subscriber asked: \"{$example->subscriber_message}\"\n";
                $prompt .= "Creator responded: \"{$example->creator_response}\"\n\n";
            }
            $prompt .= "When sending messages on behalf of the creator, try to match this style and tone.\n\n";
        }

        $prompt .= "## Important Guidelines:\n";
        $prompt .= "- Always be helpful, professional, and respectful\n";
        $prompt .= "- Protect the creator's privacy - don't share sensitive personal information\n";
        $prompt .= "- When using functions to send messages, be thoughtful and match the creator's style\n";
        $prompt .= "- If you're unsure about something, ask for clarification\n";
        $prompt .= "- Always confirm before taking actions that affect the account (like sending messages)\n";
        $prompt .= "- Focus on helping the creator manage their business effectively\n\n";

        $prompt .= "You have access to various functions to help you retrieve information and take actions. Use them when needed to provide accurate and helpful responses.";

        return $prompt;
    }

    /**
     * Get creator context data
     *
     * @param User $creator
     * @return array
     */
    public function getCreatorContext(User $creator)
    {
        $subscribers = Subscriptions::where('stripe_id', $creator->id)
            ->where('stripe_status', 'active')
            ->count();

        $posts = Updates::where('user_id', $creator->id)->count();

        return [
            'id' => $creator->id,
            'username' => $creator->username,
            'name' => $creator->name,
            'bio' => $creator->story,
            'subscribers' => $subscribers,
            'posts' => $posts,
            'balance' => number_format($creator->balance, 2),
            'currency' => config('settings.currency_code', 'USD'),
            'verified' => $creator->verified_id === 'yes',
            'free_subscription' => $creator->free_subscription === 'yes',
            'subscription_price' => (float) $creator->price,
        ];
    }

    /**
     * Get creator communication examples
     *
     * @param int $creatorId
     * @return \Illuminate\Support\Collection
     */
    protected function getCreatorExamples($creatorId)
    {
        // For now, return empty. Will implement OdevaExample model later
        // This would fetch examples the creator has provided for Odeva to learn from
        return collect([]);
    }

    /**
     * Prepare conversation context for a specific subscriber
     *
     * @param User $creator
     * @param int $subscriberId
     * @return string
     */
    public function prepareSubscriberContext(User $creator, $subscriberId)
    {
        $subscriber = User::find($subscriberId);
        
        if (!$subscriber) {
            return '';
        }

        $context = "\n## Current Conversation Context:\n";
        $context .= "You are currently chatting about subscriber: {$subscriber->name} (@{$subscriber->username})\n";
        
        // Check if they're a subscriber
        $subscription = Subscriptions::where('user_id', $subscriberId)
            ->where('stripe_id', $creator->id)
            ->where('stripe_status', 'active')
            ->first();

        if ($subscription) {
            $context .= "- Active subscriber since: {$subscription->created_at->toDateString()}\n";
        } else {
            $context .= "- Not currently a subscriber\n";
        }

        return $context;
    }
}

