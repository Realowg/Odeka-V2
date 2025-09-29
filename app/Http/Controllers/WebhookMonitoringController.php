<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Middleware\WebhookMonitoring;
use Illuminate\Support\Facades\Log;

class WebhookMonitoringController extends Controller
{
    /**
     * Show webhook monitoring dashboard
     */
    public function dashboard()
    {
        if (!auth()->user() || !auth()->user()->hasPermission('dashboard')) {
            abort(403);
        }

        $stats = WebhookMonitoring::getWebhookStats();
        $alerts = $this->checkWebhookAlerts($stats);

        return view('admin.webhook-monitoring', compact('stats', 'alerts'));
    }

    /**
     * Get webhook statistics as JSON for AJAX updates
     */
    public function getStats()
    {
        if (!auth()->user() || !auth()->user()->hasPermission('dashboard')) {
            abort(403);
        }

        $stats = WebhookMonitoring::getWebhookStats();
        $alerts = $this->checkWebhookAlerts($stats);

        return response()->json([
            'stats' => $stats,
            'alerts' => $alerts,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Check for webhook alerts based on success rates
     */
    private function checkWebhookAlerts(array $stats): array
    {
        $alerts = [];

        foreach ($stats as $endpoint => $data) {
            // Alert if success rate drops below 95% and there are attempts
            if ($data['attempts'] > 0 && $data['success_rate'] < 95) {
                $alerts[] = [
                    'level' => $data['success_rate'] < 80 ? 'critical' : 'warning',
                    'endpoint' => $endpoint,
                    'message' => "Low success rate: {$data['success_rate']}% ({$data['successes']}/{$data['attempts']})",
                    'success_rate' => $data['success_rate'],
                    'attempts' => $data['attempts']
                ];
            }

            // Alert if there are many errors
            if ($data['errors'] > 5) {
                $alerts[] = [
                    'level' => 'critical',
                    'endpoint' => $endpoint,
                    'message' => "High error count: {$data['errors']} errors in current hour",
                    'errors' => $data['errors']
                ];
            }
        }

        return $alerts;
    }

    /**
     * Test webhook endpoint manually
     */
    public function testWebhook(Request $request)
    {
        if (!auth()->user() || !auth()->user()->hasPermission('dashboard')) {
            abort(403);
        }

        $endpoint = $request->input('endpoint');
        $allowedEndpoints = ['stripe', 'paypal', 'paystack', 'mollie'];

        if (!in_array($endpoint, $allowedEndpoints)) {
            return response()->json(['error' => 'Invalid endpoint'], 400);
        }

        // Log test webhook
        Log::info("Manual webhook test initiated", [
            'endpoint' => $endpoint,
            'admin_user' => auth()->id(),
            'timestamp' => now()->toISOString()
        ]);

        return response()->json([
            'message' => "Test webhook logged for {$endpoint}",
            'timestamp' => now()->toISOString()
        ]);
    }
}