<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSettings;
use App\Models\OdevaSubscription;
use App\Models\OdevaUsageLog;
use App\Models\OdevaCostAnalytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class OdevaAdminController extends Controller
{
    public function index()
    {
        $settings = AdminSettings::first();
        
        // Get current month spending
        $currentSpending = OdevaUsageLog::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('cost');
        
        // Get active subscriptions count
        $activeSubscriptions = OdevaSubscription::where('status', 'active')->count();
        
        // Get trial subscriptions count
        $trialSubscriptions = OdevaSubscription::where('status', 'trial')->count();
        
        // Get today's usage
        $todayUsage = OdevaUsageLog::whereDate('created_at', today())->sum('cost');
        
        return view('admin.odeva.index', compact(
            'settings',
            'currentSpending',
            'activeSubscriptions',
            'trialSubscriptions',
            'todayUsage'
        ));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'odeva_provider' => 'in:anthropic,openai',
            'odeva_api_key' => 'nullable|string',
            'odeva_model' => 'nullable|string|max:100',
            'odeva_max_tokens' => 'integer|min:100|max:100000',
            'odeva_temperature' => 'numeric|min:0|max:2',
            'odeva_monthly_budget' => 'numeric|min:0',
            'odeva_creator_message_limit' => 'integer|min:0',
            'odeva_trial_days' => 'integer|min:0|max:365',
            'odeva_subscription_price' => 'numeric|min:0',
            'odeva_subscription_currency' => 'string|size:3|in:' . implode(',', array_keys(config('currencies.supported'))),
            'odeva_rate_limit' => 'integer|min:1|max:1000',
            'odeva_emergency_message' => 'nullable|string',
        ]);

        $settings = AdminSettings::first();
        
        // Handle checkboxes (they're not sent if unchecked)
        $booleanFields = [
            'odeva_enabled',
            'odeva_auto_disable_on_budget',
            'odeva_require_approval',
            'odeva_automation_enabled',
            'odeva_analytics_enabled',
            'odeva_learning_enabled',
            'odeva_subscriptions_enabled',
            'odeva_content_moderation',
            'odeva_activity_logging',
            'odeva_emergency_stop',
        ];
        
        foreach ($booleanFields as $field) {
            $validated[$field] = $request->has($field) ? 1 : 0;
        }
        
        // Encrypt API key if provided
        if (isset($validated['odeva_api_key']) && !empty($validated['odeva_api_key'])) {
            $validated['odeva_api_key'] = encrypt($validated['odeva_api_key']);
        } else {
            unset($validated['odeva_api_key']); // Don't overwrite existing key
        }

        foreach ($validated as $key => $value) {
            $settings->$key = $value;
        }
        
        $settings->save();

        return redirect()->back()->with('success', 'Odeva settings updated successfully');
    }

    public function testApiConnection()
    {
        $settings = AdminSettings::first();
        
        if (!$settings->odeva_enabled) {
            return response()->json(['success' => false, 'message' => 'Odeva is disabled']);
        }

        if (empty($settings->odeva_api_key)) {
            return response()->json(['success' => false, 'message' => 'API key not configured']);
        }

        try {
            $apiKey = decrypt($settings->odeva_api_key);
            
            if ($settings->odeva_provider === 'anthropic') {
                $response = Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])->post('https://api.anthropic.com/v1/messages', [
                    'model' => $settings->odeva_model,
                    'max_tokens' => 10,
                    'messages' => [
                        ['role' => 'user', 'content' => 'Hi']
                    ]
                ]);

                if ($response->successful()) {
                    return response()->json(['success' => true, 'message' => 'Connection successful']);
                }
            }
            
            return response()->json(['success' => false, 'message' => 'Connection failed']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function costAnalytics(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year
        
        $query = OdevaCostAnalytics::query();
        
        switch ($period) {
            case 'day':
                $query->whereDate('date', today());
                break;
            case 'week':
                $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('date', now()->month)
                      ->whereYear('date', now()->year);
                break;
            case 'year':
                $query->whereYear('date', now()->year);
                break;
        }
        
        $analytics = $query->orderBy('date', 'desc')->get();
        
        return view('admin.odeva.analytics', compact('analytics', 'period'));
    }

    public function creatorManagement()
    {
        $subscriptions = OdevaSubscription::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $settings = AdminSettings::first();
        
        return view('admin.odeva.creators', compact('subscriptions', 'settings'));
    }

    public function updateCreatorPermission(Request $request, $creatorId)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,restrict,whitelist,blacklist',
        ]);

        $settings = AdminSettings::first();
        
        switch ($validated['action']) {
            case 'approve':
                OdevaSubscription::where('creator_id', $creatorId)
                    ->update(['status' => 'active']);
                break;
                
            case 'restrict':
                OdevaSubscription::where('creator_id', $creatorId)
                    ->update(['status' => 'paused']);
                break;
                
            case 'whitelist':
                $whitelist = $settings->odeva_whitelisted_creators ?? [];
                if (!in_array($creatorId, $whitelist)) {
                    $whitelist[] = $creatorId;
                    $settings->odeva_whitelisted_creators = $whitelist;
                    $settings->save();
                }
                break;
                
            case 'blacklist':
                $blacklist = $settings->odeva_blacklisted_creators ?? [];
                if (!in_array($creatorId, $blacklist)) {
                    $blacklist[] = $creatorId;
                    $settings->odeva_blacklisted_creators = $blacklist;
                    $settings->save();
                }
                OdevaSubscription::where('creator_id', $creatorId)
                    ->update(['status' => 'cancelled']);
                break;
        }

        return redirect()->back()->with('success', 'Creator permission updated');
    }

    public function exportCostReport(Request $request)
    {
        $start = $request->get('start_date', now()->startOfMonth());
        $end = $request->get('end_date', now()->endOfMonth());
        
        $logs = OdevaUsageLog::with('creator')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $csv = "Date,Creator,Action,Tokens,Cost\n";
        
        foreach ($logs as $log) {
            $csv .= sprintf(
                "%s,%s,%s,%d,%.6f\n",
                $log->created_at->format('Y-m-d H:i:s'),
                $log->creator->name ?? 'N/A',
                $log->action,
                $log->tokens_used,
                $log->cost
            );
        }
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="odeva-costs-' . now()->format('Y-m-d') . '.csv"');
    }
}

