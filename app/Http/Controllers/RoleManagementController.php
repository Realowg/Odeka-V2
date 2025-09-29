<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class RoleManagementController extends Controller
{
    /**
     * Show role management dashboard
     */
    public function index()
    {
        if (!auth()->user() || !auth()->user()->hasEnhancedPermission('members')) {
            abort(403);
        }

        $hasUserRoles = Schema::hasTable('user_roles');

        $adminUsersQuery = User::where('role', 'admin');
        if ($hasUserRoles) {
            $adminUsersQuery = $adminUsersQuery->with(['userRole' => function($query) {
                $query->where('is_active', true);
            }]);
        }
        $adminUsers = $adminUsersQuery->paginate(20);

        $availableRoles = UserRole::getRolePermissions();

        return view('admin.role-management', [
            'adminUsers' => $adminUsers,
            'availableRoles' => $availableRoles,
            'hasUserRoles' => $hasUserRoles,
        ]);
    }

    /**
     * Assign enhanced role to user
     */
    public function assignRole(Request $request)
    {
        if (!auth()->user() || !auth()->user()->hasEnhancedPermission('members')) {
            abort(403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_name' => 'required|in:super_admin,admin,moderator,finance,support',
            'custom_permissions' => 'array|nullable'
        ]);

        $targetUser = User::findOrFail($request->user_id);

        // Prevent assigning roles to super admin (user ID 1)
        if ($targetUser->id === 1) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Cannot modify super admin role'], 400);
            }
            return back()->with('error_message', 'Cannot modify super admin role');
        }

        // Only super admin can assign super_admin role
        if ($request->role_name === 'super_admin' && auth()->id() !== 1) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Only super admin can assign super admin role'], 403);
            }
            return back()->with('error_message', 'Only super admin can assign super admin role');
        }

        DB::transaction(function () use ($request, $targetUser) {
            // Deactivate existing roles
            UserRole::where('user_id', $targetUser->id)->update(['is_active' => false]);

            // Create new role
            UserRole::create([
                'user_id' => $targetUser->id,
                'role_name' => $request->role_name,
                'permissions' => $request->custom_permissions ?? [],
                'created_by' => auth()->id(),
                'is_active' => true
            ]);
        });

        Log::info('Admin role assigned', [
            'target_user' => $targetUser->id,
            'role_name' => $request->role_name,
            'assigned_by' => auth()->id(),
            'timestamp' => now()->toISOString()
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => "Role '{$request->role_name}' assigned to {$targetUser->username}",
                'timestamp' => now()->toISOString()
            ]);
        }

        return back()->with('success_message', "Role '{$request->role_name}' assigned to {$targetUser->username}");
    }

    /**
     * Remove enhanced role (fallback to legacy)
     */
    public function removeRole(Request $request)
    {
        if (!auth()->user() || !auth()->user()->hasEnhancedPermission('members')) {
            abort(403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $targetUser = User::findOrFail($request->user_id);

        // Prevent modifying super admin
        if ($targetUser->id === 1) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Cannot modify super admin role'], 400);
            }
            return back()->with('error_message', 'Cannot modify super admin role');
        }

        UserRole::where('user_id', $targetUser->id)->update(['is_active' => false]);

        Log::info('Admin role removed', [
            'target_user' => $targetUser->id,
            'removed_by' => auth()->id(),
            'timestamp' => now()->toISOString()
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => "Enhanced role removed for {$targetUser->username}. Fallback to legacy permissions.",
                'timestamp' => now()->toISOString()
            ]);
        }

        return back()->with('success_message', "Enhanced role removed for {$targetUser->username}.");
    }

    /**
     * Migrate existing admin users to enhanced roles
     */
    public function migrateUsers(Request $request)
    {
        if (auth()->id() !== 1) {
            abort(403, 'Only super admin can perform migration');
        }

        $migrated = 0;
        $errors = [];

        $adminUsers = User::where('role', 'admin')->whereNotIn('id', [1])->get();

        foreach ($adminUsers as $user) {
            try {
                // Skip if already has enhanced role
                if (UserRole::where('user_id', $user->id)->where('is_active', true)->exists()) {
                    continue;
                }

                // Determine role based on existing permissions
                $roleName = $this->determineRoleFromPermissions($user->permissions);

                UserRole::create([
                    'user_id' => $user->id,
                    'role_name' => $roleName,
                    'permissions' => [],
                    'created_by' => auth()->id(),
                    'is_active' => true
                ]);

                $migrated++;

            } catch (\Exception $e) {
                $errors[] = "Failed to migrate user {$user->username}: " . $e->getMessage();
            }
        }

        Log::info('Admin users migration completed', [
            'migrated_count' => $migrated,
            'errors' => $errors,
            'performed_by' => auth()->id(),
            'timestamp' => now()->toISOString()
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => "Migration completed. {$migrated} users migrated.",
                'errors' => $errors,
                'timestamp' => now()->toISOString()
            ]);
        }

        $msg = "Migration completed. {$migrated} users migrated.";
        return back()->with('success_message', $msg);
    }

    /**
     * Determine role name based on existing permissions
     */
    private function determineRoleFromPermissions(?string $permissions): string
    {
        if (empty($permissions)) {
            return 'support';
        }

        if ($permissions === 'full_access') {
            return 'admin';
        }

        if ($permissions === 'limited_access') {
            return 'support';
        }

        // Parse CSV permissions
        $permissionArray = array_map('trim', explode(',', $permissions));

        // Check for finance-related permissions
        $financePerms = ['payments', 'withdrawals', 'transactions', 'deposits'];
        if (array_intersect($permissionArray, $financePerms)) {
            return 'finance';
        }

        // Check for content moderation permissions
        $moderatorPerms = ['posts', 'reports', 'comments_replies'];
        if (array_intersect($permissionArray, $moderatorPerms)) {
            return 'moderator';
        }

        // Default to admin if many permissions
        if (count($permissionArray) > 5) {
            return 'admin';
        }

        return 'support';
    }

    /**
     * Get role statistics
     */
    public function getRoleStats()
    {
        if (!auth()->user() || !auth()->user()->hasEnhancedPermission('dashboard')) {
            abort(403);
        }

        $stats = UserRole::where('is_active', true)
            ->select('role_name', DB::raw('COUNT(*) as count'))
            ->groupBy('role_name')
            ->get()
            ->pluck('count', 'role_name')
            ->toArray();

        $totalAdmins = User::where('role', 'admin')->count();
        $enhancedRoles = UserRole::where('is_active', true)->count();
        $legacyRoles = $totalAdmins - $enhancedRoles;

        return response()->json([
            'role_stats' => $stats,
            'total_admins' => $totalAdmins,
            'enhanced_roles' => $enhancedRoles,
            'legacy_roles' => $legacyRoles,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Effective permissions breakdown for a given admin user
     */
    public function effectivePermissions(Request $request)
    {
        if (!auth()->user() || !auth()->user()->hasEnhancedPermission('members')) {
            abort(403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);

        $role = null; $roleDefaults = []; $custom = []; $legacy = [];
        $hasEnhanced = false;

        try {
            if (Schema::hasTable('user_roles') && $user->userRole) {
                $hasEnhanced = true;
                $role = $user->userRole->role_name;
                $roleDefaults = UserRole::getDefaultPermissionsForRole($user->userRole->role_name);
                $custom = $user->userRole->permissions ?? [];
            }
        } catch (\Throwable $e) {
            // ignore and fallback
        }

        if (!$hasEnhanced) {
            $legacy = $user->getLegacyAdminPermissions();
        } else {
            $legacy = $user->getLegacyAdminPermissions(); // include for visibility
        }

        $effective = $user->getEffectiveAdminPermissions();

        return response()->json([
            'role' => $role,
            'role_defaults' => array_values($roleDefaults),
            'custom' => array_values($custom),
            'legacy' => array_values($legacy),
            'effective' => array_values($effective),
            'timestamp' => now()->toISOString()
        ]);
    }
}