<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Subscriptions;
use App\Models\Plans;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Api\SubscribeRequest;
use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\SubscriberResource;
use Carbon\Carbon;

class SubscriptionController extends BaseController
{
    /**
     * Get user's active subscriptions
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $subscriptions = auth()->user()->userSubscriptions()
            ->where('stripe_status', 'active')
            ->with('subscribed:id,username,name,avatar,price')
            ->latest()
            ->paginate(20);
        
        return $this->paginatedResponse($subscriptions);
    }

    /**
     * Get subscription by ID
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $subscription = Subscriptions::find($id);
        
        if (!$subscription) {
            return $this->notFoundResponse('Subscription not found');
        }

        // Check if user owns this subscription
        if ($subscription->user_id != auth()->id() && $subscription->stripe_id != auth()->id()) {
            return $this->forbiddenResponse('You do not have access to this subscription');
        }
        
        return $this->successResponse(
            new SubscriptionResource($subscription)
        );
    }

    /**
     * Subscribe to a creator
     * 
     * @param SubscribeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SubscribeRequest $request)
    {
        // Find creator
        $creator = User::where('id', $request->creator_id)
            ->where('verified_id', 'yes')
            ->first();

        if (!$creator) {
            return $this->notFoundResponse('Creator not found or not verified');
        }

        if ($creator->id == auth()->id()) {
            return $this->errorResponse('You cannot subscribe to yourself', null, 400, 'SELF_SUBSCRIBE');
        }

        // Find plan
        $plan = Plans::where('user_id', $creator->id)
            ->where('interval', $request->interval)
            ->where('status', true)
            ->first();

        if (!$plan) {
            return $this->notFoundResponse('Subscription plan not found or inactive');
        }

        // Check for existing active subscription
        $existingSubscription = auth()->user()->userSubscriptions()
            ->where('stripe_id', $creator->id)
            ->where('stripe_status', 'active')
            ->where('ends_at', '>=', now())
            ->first();

        if ($existingSubscription) {
            return $this->errorResponse('You already have an active subscription to this creator', null, 400, 'ALREADY_SUBSCRIBED');
        }

        // Check if creator allows free subscriptions
        if ($creator->free_subscription === 'yes' && $plan->price == 0) {
            // Create free subscription
            $subscription = Subscriptions::create([
                'user_id' => auth()->id(),
                'name' => $plan->name,
                'stripe_id' => $creator->id,
                'stripe_status' => 'active',
                'stripe_price' => $plan->name,
                'ends_at' => null,
            ]);

            // Send notification
            Subscriptions::sendEmailAndNotify(auth()->user()->name, $creator->id);

            return $this->successResponse(
                new SubscriptionResource($subscription),
                'Free subscription activated',
                201
            );
        }

        // For paid subscriptions, return payment intent info
        return $this->successResponse([
            'requires_payment' => true,
            'plan' => [
                'id' => $plan->id,
                'name' => $plan->name,
                'interval' => $plan->interval,
                'price' => (float) $plan->price,
                'currency' => config('settings.currency_code', 'USD'),
            ],
            'creator' => [
                'id' => $creator->id,
                'username' => $creator->username,
                'name' => $creator->name,
            ],
            'message' => 'Payment required. Use payment gateway to complete subscription.',
        ]);
    }

    /**
     * Cancel a subscription
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $subscription = Subscriptions::find($id);
        
        if (!$subscription) {
            return $this->notFoundResponse('Subscription not found');
        }

        // Check if user owns this subscription
        if ($subscription->user_id != auth()->id()) {
            return $this->forbiddenResponse('You can only cancel your own subscriptions');
        }

        // Mark subscription as ending
        $subscription->update([
            'ends_at' => now(),
            'stripe_status' => 'cancelled',
        ]);

        return $this->successResponse(null, 'Subscription cancelled successfully');
    }

    /**
     * Get user's subscribers (creators only)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribers(Request $request)
    {
        $status = $request->get('status', 'active');

        $query = Subscriptions::where('stripe_id', auth()->id())
            ->with('subscriber:id,username,name,avatar');

        if ($status !== 'all') {
            $query->where('stripe_status', $status);
        }

        $subscribers = $query->latest()->paginate(50);

        return $this->paginatedResponse($subscribers);
    }

    /**
     * Get subscriber statistics (creators only)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscriberStats()
    {
        $creatorId = auth()->id();

        $stats = [
            'total' => Subscriptions::where('stripe_id', $creatorId)->count(),
            'active' => Subscriptions::where('stripe_id', $creatorId)
                ->where('stripe_status', 'active')
                ->count(),
            'cancelled' => Subscriptions::where('stripe_id', $creatorId)
                ->where('stripe_status', 'cancelled')
                ->count(),
            'revenue_monthly' => $this->calculateMonthlyRevenue($creatorId),
            'new_this_month' => Subscriptions::where('stripe_id', $creatorId)
                ->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->count(),
        ];

        return $this->successResponse($stats);
    }

    /**
     * Get creator's subscription plans
     * 
     * @param int $creatorId
     * @return \Illuminate\Http\JsonResponse
     */
    public function creatorPlans($creatorId)
    {
        $creator = User::find($creatorId);

        if (!$creator) {
            return $this->notFoundResponse('Creator not found');
        }

        $plans = Plans::where('user_id', $creatorId)
            ->where('status', true)
            ->get();

        return $this->successResponse([
            'creator' => [
                'id' => $creator->id,
                'username' => $creator->username,
                'name' => $creator->name,
                'free_subscription' => $creator->free_subscription === 'yes',
            ],
            'plans' => $plans->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'interval' => $plan->interval,
                    'price' => (float) $plan->price,
                    'currency' => config('settings.currency_code', 'USD'),
                ];
            }),
        ]);
    }

    /**
     * Renew subscription
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function renew($id)
    {
        $subscription = Subscriptions::find($id);
        
        if (!$subscription) {
            return $this->notFoundResponse('Subscription not found');
        }

        // Check if user owns this subscription
        if ($subscription->user_id != auth()->id()) {
            return $this->forbiddenResponse('You can only renew your own subscriptions');
        }

        // Check if subscription can be renewed
        if ($subscription->stripe_status === 'active') {
            return $this->errorResponse('Subscription is already active', null, 400, 'ALREADY_ACTIVE');
        }

        // Reactivate subscription
        $subscription->update([
            'stripe_status' => 'active',
            'ends_at' => null,
        ]);

        return $this->successResponse(
            new SubscriptionResource($subscription),
            'Subscription renewed successfully'
        );
    }

    /**
     * Calculate monthly revenue for creator
     */
    protected function calculateMonthlyRevenue($creatorId)
    {
        $activeSubscriptions = Subscriptions::where('stripe_id', $creatorId)
            ->where('stripe_status', 'active')
            ->with('subscribed')
            ->get();

        $monthlyRevenue = 0;

        foreach ($activeSubscriptions as $sub) {
            // Get the plan to calculate revenue
            $plan = Plans::where('user_id', $creatorId)
                ->where('name', $sub->stripe_price)
                ->first();

            if ($plan) {
                if ($plan->interval === 'monthly') {
                    $monthlyRevenue += $plan->price;
                } elseif ($plan->interval === 'quarterly') {
                    $monthlyRevenue += $plan->price / 3;
                } elseif ($plan->interval === 'biannually') {
                    $monthlyRevenue += $plan->price / 6;
                } elseif ($plan->interval === 'yearly') {
                    $monthlyRevenue += $plan->price / 12;
                }
            }
        }

        return round($monthlyRevenue, 2);
    }
}
