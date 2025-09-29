<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WebhookMonitoring
{
    /**
     * Handle an incoming request and monitor webhook success/failure rates
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $webhookEndpoint = $this->getWebhookEndpoint($request);
        
        // Log incoming webhook
        $this->logWebhookAttempt($webhookEndpoint, $request);
        
        try {
            $response = $next($request);
            
            $duration = microtime(true) - $startTime;
            $statusCode = $response->getStatusCode();
            
            // Log success/failure
            if ($statusCode >= 200 && $statusCode < 300) {
                $this->logWebhookSuccess($webhookEndpoint, $duration);
            } else {
                $this->logWebhookFailure($webhookEndpoint, $statusCode, $duration);
            }
            
            return $response;
            
        } catch (\Exception $e) {
            $duration = microtime(true) - $startTime;
            $this->logWebhookError($webhookEndpoint, $e, $duration);
            throw $e;
        }
    }
    
    /**
     * Extract webhook endpoint identifier
     */
    private function getWebhookEndpoint(Request $request): string
    {
        $path = $request->path();
        
        // Map common webhook patterns
        if (strpos($path, 'stripe/webhook') !== false) return 'stripe';
        if (strpos($path, 'webhook/paypal') !== false) return 'paypal';
        if (strpos($path, 'webhook/paystack') !== false) return 'paystack';
        if (strpos($path, 'webhook/ccbill') !== false) return 'ccbill';
        if (strpos($path, 'webhook/mollie') !== false) return 'mollie';
        if (strpos($path, 'webhook/cardinity') !== false) return 'cardinity';
        if (strpos($path, 'webhook/nowpayments') !== false) return 'nowpayments';
        if (strpos($path, 'webhook/payku') !== false) return 'payku';
        if (strpos($path, 'webhook/coinbase') !== false) return 'coinbase';
        if (strpos($path, 'webhook/binance') !== false) return 'binance';
        if (strpos($path, 'coinpayments/ipn') !== false) return 'coinpayments';
        
        return 'unknown_webhook';
    }
    
    /**
     * Log webhook attempt
     */
    private function logWebhookAttempt(string $endpoint, Request $request): void
    {
        $cacheKey = "webhook_attempts_{$endpoint}_" . date('Y-m-d-H');
        Cache::increment($cacheKey, 1);
        Cache::put($cacheKey, Cache::get($cacheKey, 0), now()->addHours(25));
        
        Log::info("Webhook attempt", [
            'endpoint' => $endpoint,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'content_length' => $request->header('Content-Length'),
            'timestamp' => now()->toISOString()
        ]);
    }
    
    /**
     * Log successful webhook
     */
    private function logWebhookSuccess(string $endpoint, float $duration): void
    {
        $cacheKey = "webhook_success_{$endpoint}_" . date('Y-m-d-H');
        Cache::increment($cacheKey, 1);
        Cache::put($cacheKey, Cache::get($cacheKey, 0), now()->addHours(25));
        
        Log::info("Webhook success", [
            'endpoint' => $endpoint,
            'duration_ms' => round($duration * 1000, 2),
            'timestamp' => now()->toISOString()
        ]);
    }
    
    /**
     * Log failed webhook
     */
    private function logWebhookFailure(string $endpoint, int $statusCode, float $duration): void
    {
        $cacheKey = "webhook_failure_{$endpoint}_" . date('Y-m-d-H');
        Cache::increment($cacheKey, 1);
        Cache::put($cacheKey, Cache::get($cacheKey, 0), now()->addHours(25));
        
        Log::warning("Webhook failure", [
            'endpoint' => $endpoint,
            'status_code' => $statusCode,
            'duration_ms' => round($duration * 1000, 2),
            'timestamp' => now()->toISOString()
        ]);
    }
    
    /**
     * Log webhook error
     */
    private function logWebhookError(string $endpoint, \Exception $e, float $duration): void
    {
        $cacheKey = "webhook_error_{$endpoint}_" . date('Y-m-d-H');
        Cache::increment($cacheKey, 1);
        Cache::put($cacheKey, Cache::get($cacheKey, 0), now()->addHours(25));
        
        Log::error("Webhook error", [
            'endpoint' => $endpoint,
            'error' => $e->getMessage(),
            'duration_ms' => round($duration * 1000, 2),
            'timestamp' => now()->toISOString()
        ]);
    }
    
    /**
     * Get webhook statistics for monitoring dashboard
     */
    public static function getWebhookStats(): array
    {
        $endpoints = ['stripe', 'paypal', 'paystack', 'ccbill', 'mollie', 'cardinity', 'nowpayments', 'payku', 'coinbase', 'binance', 'coinpayments'];
        $currentHour = date('Y-m-d-H');
        $stats = [];
        
        foreach ($endpoints as $endpoint) {
            $attempts = Cache::get("webhook_attempts_{$endpoint}_{$currentHour}", 0);
            $successes = Cache::get("webhook_success_{$endpoint}_{$currentHour}", 0);
            $failures = Cache::get("webhook_failure_{$endpoint}_{$currentHour}", 0);
            $errors = Cache::get("webhook_error_{$endpoint}_{$currentHour}", 0);
            
            $successRate = $attempts > 0 ? round(($successes / $attempts) * 100, 2) : 0;
            
            $stats[$endpoint] = [
                'attempts' => $attempts,
                'successes' => $successes,
                'failures' => $failures,
                'errors' => $errors,
                'success_rate' => $successRate
            ];
        }
        
        return $stats;
    }
}