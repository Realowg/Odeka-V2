<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $fillable = ['user_id', 'role_name', 'permissions', 'created_by', 'is_active'];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Define role permissions mapping
     */
    public static function getRolePermissions(): array
    {
        return [
            'super_admin' => [
                'dashboard', 'members', 'payments', 'settings', 'categories', 'posts', 
                'reports', 'withdrawals', 'subscriptions', 'transactions', 'pages',
                'verification_requests', 'social_login', 'google', 'languages', 
                'maintenance_mode', 'blog', 'deposits', 'custom_css_js', 'pwa',
                'shop_categories', 'push_notifications', 'referrals', 'shop',
                'products', 'sales', 'tax', 'countries_states', 'announcements',
                'live_streaming', 'stories', 'comments_replies', 'messages',
                'advertising', 'gifts'
            ],
            'admin' => [
                'dashboard', 'members', 'settings', 'categories', 'posts', 
                'reports', 'pages', 'verification_requests', 'languages', 
                'blog', 'custom_css_js', 'shop_categories', 'announcements',
                'stories', 'comments_replies', 'advertising'
            ],
            'moderator' => [
                'dashboard', 'posts', 'reports', 'comments_replies', 'stories',
                'verification_requests'
            ],
            'finance' => [
                'dashboard', 'payments', 'withdrawals', 'subscriptions', 
                'transactions', 'deposits', 'sales', 'referrals'
            ],
            'support' => [
                'dashboard' // Read-only access handled by middleware
            ]
        ];
    }

    /**
     * Return default permissions for a role
     */
    public static function getDefaultPermissionsForRole(string $roleName): array
    {
        $map = self::getRolePermissions();
        return $map[$roleName] ?? [];
    }

    /**
     * Check if role has specific permission
     */
    public static function roleHasPermission(string $role, string $permission): bool
    {
        $rolePermissions = self::getRolePermissions();
        return isset($rolePermissions[$role]) && in_array($permission, $rolePermissions[$role]);
    }

    /**
     * Get all available permissions
     */
    public static function getAllPermissions(): array
    {
        $rolePermissions = self::getRolePermissions();
        $allPermissions = [];
        
        foreach ($rolePermissions as $permissions) {
            $allPermissions = array_merge($allPermissions, $permissions);
        }
        
        return array_unique($allPermissions);
    }

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to creator (admin who assigned the role)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}