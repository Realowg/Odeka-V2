<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Transactions;
use App\Models\Deposits;
use App\Models\Withdrawals;
use App\Models\User;
use App\Models\Tips;
use Illuminate\Http\Request;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\WalletResource;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PaymentController extends BaseController
{
    /**
     * Get wallet balance and info
     */
    public function wallet()
    {
        $user = auth()->user();

        return $this->successResponse([
            'balance' => (float) $user->balance,
            'wallet' => (float) $user->wallet,
            'currency' => config('settings.currency_code', 'USD'),
            'pending_balance' => $this->getPendingBalance($user->id),
        ]);
    }

    /**
     * Get transactions
     */
    public function transactions(Request $request)
    {
        $type = $request->get('type'); // 'all', 'credit', 'debit', 'subscription', 'tip', 'ppv'
        
        $query = Transactions::where('user_id', auth()->id())
            ->orWhere('subscribed', auth()->id());

        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }

        $transactions = $query->latest()
            ->paginate(50);

        return $this->paginatedResponse($transactions);
    }

    /**
     * Get earnings
     */
    public function earnings(Request $request)
    {
        $period = $request->get('period', 'all'); // 'today', 'week', 'month', 'year', 'all'
        
        $query = Transactions::where('subscribed', auth()->id())
            ->where('approved', 1);

        $query = $this->applyPeriodFilter($query, $period);

        $earnings = [
            'period' => $period,
            'total' => (float) $query->sum('earning_net_user'),
            'count' => $query->count(),
            'by_type' => [
                'subscriptions' => (float) $query->where('type', 'subscription')->sum('earning_net_user'),
                'tips' => (float) $query->where('type', 'tip')->sum('earning_net_user'),
                'ppv' => (float) $query->where('type', 'ppv')->sum('earning_net_user'),
            ],
        ];

        return $this->successResponse($earnings);
    }

    /**
     * Send tip to creator
     */
    public function tip(Request $request)
    {
        $request->validate([
            'creator_id' => 'required|integer|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'message' => 'sometimes|string|max:500',
        ]);

        $creator = User::find($request->creator_id);
        $amount = (float) $request->amount;

        // Check balance
        if (auth()->user()->wallet < $amount) {
            return $this->errorResponse('Insufficient balance', null, 400, 'INSUFFICIENT_BALANCE');
        }

        // Calculate fees
        $fee = config('settings.fee_commission', 0) / 100;
        $creatorEarning = $amount * (1 - $fee);
        $adminFee = $amount * $fee;

        // Deduct from sender
        auth()->user()->decrement('wallet', $amount);

        // Add to creator
        $creator->increment('balance', $creatorEarning);

        // Create transaction
        $transaction = Transactions::create([
            'txn_id' => Str::random(25),
            'user_id' => auth()->id(),
            'subscribed' => $creator->id,
            'subscriptions_id' => 0,
            'earning_net_user' => $creatorEarning,
            'earning_net_admin' => $adminFee,
            'payment_gateway' => 'wallet',
            'approved' => 1,
            'amount' => $amount,
            'type' => 'tip',
            'percentage_applied' => config('settings.fee_commission', 0),
            'referred_commission' => 0,
            'taxes' => '',
        ]);

        return $this->successResponse([
            'transaction_id' => $transaction->id,
            'amount' => $amount,
            'creator_received' => $creatorEarning,
            'new_wallet_balance' => (float) auth()->user()->wallet,
        ], 'Tip sent successfully', 201);
    }

    /**
     * Pay for PPV content
     */
    public function ppv(Request $request)
    {
        $request->validate([
            'post_id' => 'sometimes|integer|exists:updates,id',
            'message_id' => 'sometimes|integer|exists:messages,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $amount = (float) $request->amount;

        // Check balance
        if (auth()->user()->wallet < $amount) {
            return $this->errorResponse('Insufficient balance', null, 400, 'INSUFFICIENT_BALANCE');
        }

        // Determine creator
        $creatorId = null;
        if ($request->post_id) {
            $post = \App\Models\Updates::find($request->post_id);
            $creatorId = $post->user_id;
        } elseif ($request->message_id) {
            $message = \App\Models\Messages::find($request->message_id);
            $creatorId = $message->from_user_id;
        }

        if (!$creatorId) {
            return $this->errorResponse('Invalid PPV purchase', null, 400, 'INVALID_PPV');
        }

        $creator = User::find($creatorId);

        // Calculate fees
        $fee = config('settings.fee_commission', 0) / 100;
        $creatorEarning = $amount * (1 - $fee);
        $adminFee = $amount * $fee;

        // Deduct from buyer
        auth()->user()->decrement('wallet', $amount);

        // Add to creator
        $creator->increment('balance', $creatorEarning);

        // Create transaction
        $transaction = Transactions::create([
            'txn_id' => Str::random(25),
            'user_id' => auth()->id(),
            'subscribed' => $creator->id,
            'subscriptions_id' => 0,
            'earning_net_user' => $creatorEarning,
            'earning_net_admin' => $adminFee,
            'payment_gateway' => 'wallet',
            'approved' => 1,
            'amount' => $amount,
            'type' => 'ppv',
            'percentage_applied' => config('settings.fee_commission', 0),
            'referred_commission' => 0,
            'taxes' => '',
        ]);

        // Create purchase record if needed
        if ($request->post_id) {
            \App\Models\Purchases::create([
                'user_id' => auth()->id(),
                'updates_id' => $request->post_id,
                'transactions_id' => $transaction->id,
            ]);
        }

        return $this->successResponse([
            'transaction_id' => $transaction->id,
            'amount' => $amount,
            'unlocked' => true,
        ], 'PPV content unlocked', 201);
    }

    /**
     * Add funds to wallet
     */
    public function addFunds(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_gateway' => 'required|string|in:stripe,paypal,kkiapay,wallet',
        ]);

        // For now, return payment intent info
        // Actual payment processing would be done by payment gateway webhooks

        return $this->successResponse([
            'requires_payment' => true,
            'amount' => (float) $request->amount,
            'payment_gateway' => $request->payment_gateway,
            'message' => 'Complete payment via payment gateway webhook',
        ]);
    }

    /**
     * Request withdrawal
     */
    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'gateway' => 'required|string|in:bank,paypal,stripe,payoneer,zelle',
        ]);

        $amount = (float) $request->amount;
        $user = auth()->user();

        // Check balance
        if ($user->balance < $amount) {
            return $this->errorResponse('Insufficient balance', null, 400, 'INSUFFICIENT_BALANCE');
        }

        // Check minimum withdrawal
        $minWithdrawal = config('settings.min_withdrawal', 10);
        if ($amount < $minWithdrawal) {
            return $this->errorResponse("Minimum withdrawal is {$minWithdrawal}", null, 400, 'BELOW_MINIMUM');
        }

        // Deduct from balance
        $user->decrement('balance', $amount);

        // Create withdrawal request
        $withdrawal = Withdrawals::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'gateway' => $request->gateway,
            'status' => 'pending',
            'date' => now(),
        ]);

        return $this->successResponse([
            'withdrawal_id' => $withdrawal->id,
            'amount' => $amount,
            'gateway' => $request->gateway,
            'status' => 'pending',
            'new_balance' => (float) $user->balance,
        ], 'Withdrawal request submitted', 201);
    }

    /**
     * Get withdrawal history
     */
    public function withdrawals()
    {
        $withdrawals = Withdrawals::where('user_id', auth()->id())
            ->latest()
            ->paginate(50);

        return $this->paginatedResponse($withdrawals);
    }

    /**
     * Get deposits history
     */
    public function deposits()
    {
        $deposits = Deposits::where('user_id', auth()->id())
            ->latest()
            ->paginate(50);

        return $this->paginatedResponse($deposits);
    }

    /**
     * Get available payment methods
     */
    public function methods()
    {
        $methods = \App\Models\PaymentGateways::where('enabled', '1')->get();

        return $this->successResponse([
            'methods' => $methods->map(function($method) {
                return [
                    'id' => $method->id,
                    'name' => $method->name,
                    'type' => $method->type,
                    'fee' => (float) $method->fee,
                ];
            }),
        ]);
    }

    /**
     * Get pending balance
     */
    protected function getPendingBalance($userId)
    {
        return (float) Transactions::where('subscribed', $userId)
            ->where('approved', 0)
            ->sum('earning_net_user');
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
