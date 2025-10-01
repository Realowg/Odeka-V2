<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_settings', function (Blueprint $table) {
            // Core Admin Controls
            $table->boolean('odeva_enabled')->default(false);
            $table->string('odeva_provider', 50)->default('anthropic');
            $table->text('odeva_api_key')->nullable();
            $table->string('odeva_model', 100)->default('claude-3-5-sonnet-20241022');
            $table->integer('odeva_max_tokens')->default(4096);
            $table->decimal('odeva_temperature', 3, 2)->default(0.7);
            
            // Budget & Cost Management
            $table->decimal('odeva_monthly_budget', 10, 2)->default(0);
            $table->decimal('odeva_current_spending', 10, 2)->default(0);
            $table->boolean('odeva_auto_disable_on_budget')->default(true);
            $table->date('odeva_budget_reset_date')->nullable();
            
            // Creator Management
            $table->boolean('odeva_require_approval')->default(true);
            $table->integer('odeva_creator_message_limit')->default(1000);
            $table->json('odeva_whitelisted_creators')->nullable();
            $table->json('odeva_blacklisted_creators')->nullable();
            
            // Feature Toggles
            $table->boolean('odeva_automation_enabled')->default(true);
            $table->boolean('odeva_analytics_enabled')->default(true);
            $table->boolean('odeva_learning_enabled')->default(true);
            $table->boolean('odeva_subscriptions_enabled')->default(true);
            $table->integer('odeva_trial_days')->default(14);
            
            // Subscription Plans
            $table->decimal('odeva_subscription_price', 10, 2)->default(29.99);
            $table->string('odeva_subscription_currency', 3)->default('USD');
            
            // Safety & Moderation
            $table->boolean('odeva_content_moderation')->default(true);
            $table->boolean('odeva_activity_logging')->default(true);
            $table->integer('odeva_rate_limit')->default(20);
            
            // Emergency Controls
            $table->boolean('odeva_emergency_stop')->default(false);
            $table->text('odeva_emergency_message')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('admin_settings', function (Blueprint $table) {
            $table->dropColumn([
                'odeva_enabled', 'odeva_provider', 'odeva_api_key', 'odeva_model',
                'odeva_max_tokens', 'odeva_temperature', 'odeva_monthly_budget',
                'odeva_current_spending', 'odeva_auto_disable_on_budget',
                'odeva_budget_reset_date', 'odeva_require_approval',
                'odeva_creator_message_limit', 'odeva_whitelisted_creators',
                'odeva_blacklisted_creators', 'odeva_automation_enabled',
                'odeva_analytics_enabled', 'odeva_learning_enabled',
                'odeva_subscriptions_enabled', 'odeva_trial_days',
                'odeva_subscription_price', 'odeva_subscription_currency',
                'odeva_content_moderation', 'odeva_activity_logging',
                'odeva_rate_limit', 'odeva_emergency_stop', 'odeva_emergency_message',
            ]);
        });
    }
};

