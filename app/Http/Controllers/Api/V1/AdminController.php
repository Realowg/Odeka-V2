<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\AdminSettings;
use App\Models\Reports;
use App\Models\Transactions;
use App\Models\Withdrawals;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends BaseController
{
    /**
     * Check if user is admin
     */
    protected function checkAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            return $this->forbiddenResponse('Admin access required');
        }
        return null;
    }

    /**
     * Get dashboard statistics
     */
    public function dashboard()
    {
        if ($error = $this->checkAdmin()) return $error;

        $stats = [
            'users' => [
                'total' => User::count(),
                'active' => User::where('status', 'active')->count(),
                'creators' => User::where('verified_id', 'yes')->count(),
                'new_today' => User::whereDate('date', Carbon::today())->count(),
                'new_this_month' => User::where('date', '>=', Carbon::now()->startOfMonth())->count(),
            ],
            'revenue' => [
                'total' => (float) Transactions::where('approved', 1)->sum('earning_net_admin'),
                'today' => (float) Transactions::where('approved', 1)->whereDate('created_at', Carbon::today())->sum('earning_net_admin'),
                'this_month' => (float) Transactions::where('approved', 1)->where('created_at', '>=', Carbon::now()->startOfMonth())->sum('earning_net_admin'),
            ],
            'transactions' => [
                'total' => Transactions::count(),
                'pending' => Transactions::where('approved', 0)->count(),
                'approved' => Transactions::where('approved', 1)->count(),
            ],
            'withdrawals' => [
                'pending' => Withdrawals::where('status', 'pending')->count(),
                'total_amount' => (float) Withdrawals::where('status', 'pending')->sum('amount'),
            ],
            'reports' => [
                'pending' => Reports::where('status', 'pending')->count(),
            ],
        ];

        return $this->successResponse($stats);
    }

    /**
     * Get users list
     */
    public function users(Request $request)
    {
        if ($error = $this->checkAdmin()) return $error;

        $query = User::query();

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('verified')) {
            $query->where('verified_id', $request->verified);
        }

        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('username', 'LIKE', "%{$request->search}%")
                  ->orWhere('email', 'LIKE', "%{$request->search}%")
                  ->orWhere('name', 'LIKE', "%{$request->search}%");
            });
        }

        $users = $query->latest()->paginate(50);

        return $this->paginatedResponse($users);
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, $id)
    {
        if ($error = $this->checkAdmin()) return $error;

        $user = User::find($id);

        if (!$user) {
            return $this->notFoundResponse('User not found');
        }

        $user->update($request->only([
            'status',
            'verified_id',
            'role',
            'featured',
            'balance',
            'wallet',
        ]));

        return $this->successResponse($user, 'User updated successfully');
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        if ($error = $this->checkAdmin()) return $error;

        $user = User::find($id);

        if (!$user) {
            return $this->notFoundResponse('User not found');
        }

        $user->delete();

        return $this->successResponse(null, 'User deleted successfully');
    }

    /**
     * Get reports
     */
    public function reports(Request $request)
    {
        if ($error = $this->checkAdmin()) return $error;

        $query = Reports::with(['user:id,username,name', 'post:id,description']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->latest()->paginate(50);

        return $this->paginatedResponse($reports);
    }

    /**
     * Handle report
     */
    public function handleReport(Request $request, $id)
    {
        if ($error = $this->checkAdmin()) return $error;

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'action' => 'sometimes|in:delete_post,ban_user,warn_user,none',
        ]);

        $report = Reports::find($id);

        if (!$report) {
            return $this->notFoundResponse('Report not found');
        }

        $report->update(['status' => $request->status]);

        // Handle actions
        if ($request->action === 'delete_post' && $report->updates_id) {
            \App\Models\Updates::where('id', $report->updates_id)->update(['status' => 'deleted']);
        } elseif ($request->action === 'ban_user' && $report->user_id) {
            User::where('id', $report->user_id)->update(['status' => 'suspended']);
        }

        return $this->successResponse($report, 'Report handled successfully');
    }

    /**
     * Get transactions
     */
    public function transactions(Request $request)
    {
        if ($error = $this->checkAdmin()) return $error;

        $query = Transactions::with(['user:id,username,name', 'subscribed:id,username,name']);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('approved', $request->status === 'approved' ? 1 : 0);
        }

        $transactions = $query->latest()->paginate(50);

        return $this->paginatedResponse($transactions);
    }

    /**
     * Get settings
     */
    public function settings()
    {
        if ($error = $this->checkAdmin()) return $error;

        $settings = AdminSettings::first();

        return $this->successResponse($settings);
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        if ($error = $this->checkAdmin()) return $error;

        $settings = AdminSettings::first();

        $settings->update($request->all());

        return $this->successResponse($settings, 'Settings updated successfully');
    }

    /**
     * Get analytics
     */
    public function analytics(Request $request)
    {
        if ($error = $this->checkAdmin()) return $error;

        $period = $request->get('period', '30'); // days

        $startDate = Carbon::now()->subDays($period);

        $analytics = [
            'users_growth' => User::where('date', '>=', $startDate)
                ->select(DB::raw('DATE(date) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),

            'revenue_trend' => Transactions::where('created_at', '>=', $startDate)
                ->where('approved', 1)
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('sum(earning_net_admin) as revenue'))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),

            'top_creators' => User::where('verified_id', 'yes')
                ->select('id', 'username', 'name', 'balance')
                ->orderBy('balance', 'desc')
                ->take(10)
                ->get(),

            'transactions_by_type' => Transactions::where('created_at', '>=', $startDate)
                ->select('type', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
                ->groupBy('type')
                ->get(),
        ];

        return $this->successResponse($analytics);
    }

    /**
     * Get withdrawals
     */
    public function withdrawals(Request $request)
    {
        if ($error = $this->checkAdmin()) return $error;

        $query = Withdrawals::with('user:id,username,name,email');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $withdrawals = $query->latest()->paginate(50);

        return $this->paginatedResponse($withdrawals);
    }

    /**
     * Update withdrawal status
     */
    public function updateWithdrawal(Request $request, $id)
    {
        if ($error = $this->checkAdmin()) return $error;

        $request->validate([
            'status' => 'required|in:pending,paid,rejected',
        ]);

        $withdrawal = Withdrawals::find($id);

        if (!$withdrawal) {
            return $this->notFoundResponse('Withdrawal not found');
        }

        $withdrawal->update(['status' => $request->status]);

        // If rejected, refund to user balance
        if ($request->status === 'rejected') {
            $withdrawal->user->increment('balance', $withdrawal->amount);
        }

        return $this->successResponse($withdrawal, 'Withdrawal updated successfully');
    }
}
