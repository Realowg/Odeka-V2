<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogApiRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        $duration = microtime(true) - $startTime;
        
        // Log API request asynchronously
        try {
            DB::table('api_logs')->insert([
                'user_id' => auth()->id(),
                'method' => $request->method(),
                'endpoint' => $request->path(),
                'status_code' => $response->status(),
                'request_body' => $this->sanitizeData($request->all()),
                'response_body' => $this->sanitizeResponse($response),
                'ip_address' => $request->ip(),
                'duration' => $duration,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log API request: ' . $e->getMessage());
        }
        
        return $response;
    }

    /**
     * Sanitize request data (remove sensitive fields)
     */
    private function sanitizeData(array $data): ?string
    {
        $sensitiveFields = ['password', 'password_confirmation', 'token', 'api_key', 'secret'];
        
        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '***REDACTED***';
            }
        }
        
        return json_encode($data);
    }

    /**
     * Sanitize response (limit size)
     */
    private function sanitizeResponse($response): ?string
    {
        $content = $response->getContent();
        
        // Limit to 10KB
        if (strlen($content) > 10240) {
            return substr($content, 0, 10240) . '... (truncated)';
        }
        
        return $content;
    }
}

