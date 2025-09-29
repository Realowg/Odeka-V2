<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use App\Models\UserRole;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class EnhancedRole
{
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request with enhanced role checking
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            return redirect()->guest('login')
                ->with(['login_required' => trans('auth.login_required')]);
        }

        $user = $this->auth->user();
        $routeName = $request->route()->getName();

        // For super admin (user ID 1), allow everything
        if ($user->id === 1) {
            return $next($request);
        }

        // Check if user is admin role
        if ($user->role !== 'admin') {
            return redirect('/');
        }

        // Enhanced permission checking with fallback to legacy system
        if (!$this->hasPermission($user, $routeName, $request)) {
            // Log access attempt for monitoring
            Log::warning('Admin access denied', [
                'user_id' => $user->id,
                'username' => $user->username,
                'route' => $routeName,
                'method' => $request->method(),
                'ip' => $request->ip(),
                'timestamp' => now()->toISOString()
            ]);

            if ($request->isMethod('post')) {
                return redirect()->back()->withUnauthorized(trans('general.unauthorized_action'));
            } else {
                abort(403, 'Insufficient permissions for this action');
            }
        }

        return $next($request);
    }

    /**
     * Enhanced permission checking with fallback
     */
    private function hasPermission($user, string $routeName, $request): bool
    {
        // Skip permission check for dashboard route
        if ($routeName === 'dashboard') {
            return true;
        }

        // Try enhanced role system first
        $enhancedRole = $this->getEnhancedUserRole($user);
        if ($enhancedRole) {
            return $this->checkEnhancedPermission($enhancedRole, $routeName, $request);
        }

        // Fallback to legacy permission system
        return $this->checkLegacyPermission($user, $routeName, $request);
    }

    /**
     * Get enhanced role for user
     */
    private function getEnhancedUserRole($user)
    {
        try {
            if (!Schema::hasTable('user_roles')) {
                return null;
            }

            return UserRole::where('user_id', $user->id)
                ->where('is_active', true)
                ->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Check permission using enhanced role system
     */
    private function checkEnhancedPermission($userRole, string $routeName, $request): bool
    {
        // Support role gets read-only access
        if ($userRole->role_name === 'support' && $request->isMethod('post')) {
            return false;
        }

        // Check if role has permission for this route
        return UserRole::roleHasPermission($userRole->role_name, $routeName) ||
               in_array($routeName, $userRole->permissions ?? []);
    }

    /**
     * Legacy permission checking (existing system)
     */
    private function checkLegacyPermission($user, string $routeName, $request): bool
    {
        // GET request permission check
        if ($request->isMethod('get') && !$user->hasPermission($routeName)) {
            return false;
        }

        // POST request permission check for limited access
        if ($user->permissions === 'limited_access' && 
            $request->isMethod('post') && 
            $routeName !== 'dashboard.earnings') {
            return false;
        }

        return true;
    }
}