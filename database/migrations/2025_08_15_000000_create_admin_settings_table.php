<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_settings', function (Blueprint $table) {
            $table->id();
            
            // Basic Settings
            $table->string('title')->default('Odeka V2');
            $table->string('email_admin')->default('admin@example.com');
            $table->string('link_terms')->default('#');
            $table->string('link_privacy')->default('#');
            $table->string('link_cookies')->default('#');
            $table->string('date_format')->default('M d, Y');
            $table->string('theme')->default('light');
            
            // Security & Verification
            $table->enum('captcha', ['on', 'off'])->default('off');
            $table->boolean('email_verification')->default(false);
            $table->boolean('registration_active')->default(true);
            $table->boolean('disable_login_register_email')->default(false);
            $table->boolean('account_verification')->default(false);
            $table->enum('requests_verify_account', ['on', 'off'])->default('off');
            $table->enum('captcha_contact', ['on', 'off'])->default('off');
            
            // Display Settings
            $table->enum('show_counter', ['on', 'off'])->default('off');
            $table->enum('widget_creators_featured', ['on', 'off'])->default('off');
            $table->enum('hide_admin_profile', ['on', 'off'])->default('off');
            $table->enum('earnings_simulator', ['on', 'off'])->default('off');
            $table->enum('watermark', ['on', 'off'])->default('off');
            $table->enum('alert_adult', ['on', 'off'])->default('off');
            $table->enum('disable_banner_cookies', ['on', 'off'])->default('off');
            $table->enum('disable_contact', ['on', 'off'])->default('off');
            $table->enum('disable_new_post_notification', ['on', 'off'])->default('off');
            $table->enum('disable_search_creators', ['on', 'off'])->default('off');
            $table->enum('search_creators_genders', ['on', 'off'])->default('off');
            $table->enum('generate_qr_code', ['on', 'off'])->default('off');
            $table->enum('disable_free_post', ['on', 'off'])->default('off');
            $table->enum('disable_explore_section', ['on', 'off'])->default('off');
            $table->enum('disable_creators_section', ['on', 'off'])->default('off');
            
            // Content Settings
            $table->text('genders');
            $table->enum('who_can_see_content', ['all', 'users'])->default('all');
            $table->enum('users_can_edit_post', ['on', 'off'])->default('off');
            $table->enum('allow_zip_files', ['on', 'off'])->default('off');
            $table->enum('zip_verification_creator', ['on', 'off'])->default('off');
            $table->enum('allow_scheduled_posts', ['on', 'off'])->default('off');
            $table->enum('allow_epub_files', ['on', 'off'])->default('off');
            $table->enum('gifts', ['on', 'off'])->default('off');
            $table->enum('users_can_delete_messages', ['on', 'off'])->default('off');
            
            // User Management
            $table->enum('autofollow_admin', ['on', 'off'])->default('off');
            $table->enum('allow_creators_deactivate_profile', ['on', 'off'])->default('off');
            $table->integer('delete_old_users_inactive')->default(0);
            
            // Financial Settings
            $table->string('currency_symbol')->default('$');
            $table->string('currency_code')->default('USD');
            $table->enum('currency_position', ['left', 'left_space', 'right', 'right_space'])->default('left');
            $table->enum('decimal_format', ['dot', 'comma'])->default('dot');
            
            // Amounts and Limits
            $table->decimal('min_subscription_amount', 8, 2)->default(1.00);
            $table->decimal('max_subscription_amount', 8, 2)->default(1000.00);
            $table->decimal('min_tip_amount', 8, 2)->default(1.00);
            $table->decimal('max_tip_amount', 8, 2)->default(1000.00);
            $table->decimal('min_ppv_amount', 8, 2)->default(1.00);
            $table->decimal('max_ppv_amount', 8, 2)->default(1000.00);
            $table->decimal('min_deposits_amount', 8, 2)->default(5.00);
            $table->decimal('max_deposits_amount', 8, 2)->default(1000.00);
            $table->decimal('min_price_product', 8, 2)->default(1.00);
            $table->decimal('max_price_product', 8, 2)->default(1000.00);
            
            // Commission and Fees
            $table->decimal('fee_commission', 5, 2)->default(20.00);
            $table->decimal('percentage_referred', 5, 2)->default(5.00);
            $table->decimal('referral_transaction_limit', 8, 2)->default(100.00);
            
            // Withdrawal Settings
            $table->decimal('amount_min_withdrawal', 8, 2)->default(50.00);
            $table->decimal('amount_max_withdrawal', 8, 2)->default(1000.00);
            $table->integer('specific_day_payment_withdrawals')->default(1);
            $table->integer('days_process_withdrawals')->default(7);
            
            // Content Limits
            $table->integer('update_length')->default(1000);
            $table->integer('file_size_allowed')->default(10240); // KB
            $table->integer('file_size_allowed_verify_account')->default(5120); // KB
            $table->integer('video_length')->default(60); // seconds
            
            // Features
            $table->enum('disable_tips', ['on', 'off'])->default('off');
            $table->enum('referral_system', ['on', 'off'])->default('off');
            $table->enum('shop', ['on', 'off'])->default('off');
            $table->enum('allow_free_items_shop', ['on', 'off'])->default('off');
            $table->enum('allow_external_links_shop', ['on', 'off'])->default('off');
            $table->enum('digital_product_sale', ['on', 'off'])->default('off');
            $table->enum('custom_content', ['on', 'off'])->default('off');
            $table->enum('physical_products', ['on', 'off'])->default('off');
            
            // Wallet & Payments
            $table->enum('disable_wallet', ['on', 'off'])->default('off');
            $table->enum('wallet_format', ['real_money', 'credits', 'points', 'tokens'])->default('real_money');
            $table->enum('tax_on_wallet', ['on', 'off'])->default('off');
            
            // Stripe Connect
            $table->boolean('stripe_connect')->default(false);
            $table->text('stripe_connect_countries')->nullable();
            
            // Default Images
            $table->string('avatar')->default('default.jpg');
            $table->string('cover_default')->nullable();
            $table->string('logo')->nullable();
            $table->string('logo_2')->nullable();
            $table->string('favicon')->nullable();
            
            // Home Page Settings
            $table->integer('home_style')->default(0);
            $table->string('title_site')->nullable();
            $table->text('description_site')->nullable();
            $table->text('keywords')->nullable();
            
            // Social Login
            $table->enum('facebook_login', ['on', 'off'])->default('off');
            $table->enum('google_login', ['on', 'off'])->default('off');
            $table->enum('twitter_login', ['on', 'off'])->default('off');
            
            // Live Streaming
            $table->enum('live_streaming', ['on', 'off'])->default('off');
            $table->enum('live_streaming_private', ['on', 'off'])->default('off');
            $table->decimal('live_streaming_minimum_price', 8, 2)->default(1.00);
            $table->integer('live_streaming_max_minutes')->default(60);
            
            // Stories
            $table->enum('stories', ['on', 'off'])->default('off');
            $table->integer('story_duration')->default(24); // hours
            $table->integer('story_max_videos')->default(5);
            
            // Push Notifications
            $table->enum('push_notification_status', ['on', 'off'])->default('off');
            $table->string('onesignal_appid')->nullable();
            $table->string('onesignal_restapi')->nullable();
            
            // Custom CSS/JS
            $table->longText('custom_css')->nullable();
            $table->longText('custom_js')->nullable();
            
            // Maintenance
            $table->enum('maintenance_mode', ['on', 'off'])->default('off');
            $table->text('maintenance_mode_message')->nullable();
            
            // Video Encoding
            $table->enum('video_encoding', ['on', 'off'])->default('off');
            $table->string('coconut_key')->nullable();
            
            // Email Settings
            $table->string('email_encoding')->default('utf-8');
            $table->enum('email_no_reply_name', ['app_name', 'custom'])->default('app_name');
            $table->string('email_no_reply_custom')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_settings');
    }
};
