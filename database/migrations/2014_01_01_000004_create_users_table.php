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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('username', 50);
            $table->string('countries_id', 25);
            $table->string('password');
            $table->string('email')->unique();
            $table->timestamp('date')->useCurrent();
            $table->string('avatar', 70);
            $table->string('cover', 70);
            $table->string('status', 200)->default('active');
            $table->enum('role', ['normal', 'admin'])->default('normal');
            $table->enum('permission', ['all', 'none'])->default('none');
            $table->rememberToken();
            $table->string('token', 80);
            $table->string('confirmation_code', 125);
            $table->string('paypal_account', 200);
            $table->string('payment_gateway', 50);
            $table->text('bank');
            $table->enum('featured', ['yes', 'no'])->default('no');
            $table->timestamp('featured_date')->nullable();
            $table->string('about', 200);
            $table->text('story');
            $table->string('profession', 200);
            $table->string('oauth_uid');
            $table->string('oauth_provider');
            $table->string('categories_id');
            $table->string('website', 200);
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->enum('verified_id', ['yes', 'no', 'reject'])->default('no');
            $table->string('address', 200);
            $table->string('city', 150);
            $table->string('zip', 50);
            $table->string('facebook', 200);
            $table->string('twitter', 200);
            $table->string('instagram', 200);
            $table->string('youtube', 200);
            $table->string('pinterest', 200);
            $table->string('github', 200);
            $table->timestamp('last_seen')->nullable();
            $table->enum('email_new_subscriber', ['yes', 'no'])->default('yes');
            $table->string('plan');
            $table->enum('notify_new_subscriber', ['yes', 'no'])->default('yes');
            $table->enum('notify_liked_post', ['yes', 'no'])->default('yes');
            $table->enum('notify_commented_post', ['yes', 'no'])->default('yes');
            $table->string('company', 50);
            $table->enum('post_locked', ['yes', 'no'])->default('yes');
            $table->string('ip', 40);
            $table->enum('dark_mode', ['on', 'off'])->default('off');
            $table->string('gender', 50);
            $table->string('birthdate', 30);
            $table->enum('allow_download_files', ['no', 'yes'])->default('no');
            $table->string('language', 10);
            $table->enum('free_subscription', ['yes', 'no'])->default('no');
            $table->decimal('wallet', 10, 2)->default(0);
            $table->string('tiktok', 200);
            $table->string('snapchat', 200);
            $table->string('paystack_plan', 100);
            $table->string('paystack_authorization_code', 100);
            $table->unsignedInteger('paystack_last4');
            $table->string('paystack_exp', 50);
            $table->string('paystack_card_brand', 25);
            $table->enum('notify_new_tip', ['yes', 'no'])->default('yes');
            $table->enum('hide_profile', ['yes', 'no'])->default('no');
            $table->enum('hide_last_seen', ['yes', 'no'])->default('no');
            $table->string('last_login', 250);
            $table->enum('hide_count_subscribers', ['yes', 'no'])->default('no');
            $table->enum('hide_my_country', ['yes', 'no'])->default('no');
            $table->enum('show_my_birthdate', ['yes', 'no'])->default('no');
            $table->enum('notify_new_post', ['yes', 'no'])->default('yes');
            $table->enum('notify_email_new_post', ['yes', 'no'])->default('yes');
            $table->unsignedInteger('custom_fee');
            $table->enum('hide_name', ['yes', 'no'])->default('no');
            $table->enum('birthdate_changed', ['yes', 'no'])->default('no');
            $table->enum('email_new_tip', ['yes', 'no'])->default('yes');
            $table->enum('email_new_ppv', ['yes', 'no'])->default('yes');
            $table->enum('notify_new_ppv', ['yes', 'no'])->default('yes');
            $table->enum('active_status_online', ['yes', 'no'])->default('yes');
            $table->string('payoneer_account', 200);
            $table->string('zelle_account', 200);
            $table->enum('notify_liked_comment', ['yes', 'no'])->default('yes');
            $table->text('permissions');
            $table->text('blocked_countries');
            $table->enum('two_factor_auth', ['yes', 'no'])->default('no');
            $table->enum('notify_live_streaming', ['yes', 'no'])->default('yes');
            $table->enum('notify_mentions', ['yes', 'no'])->default('yes');
            $table->string('stripe_connect_id')->nullable();
            $table->boolean('completed_stripe_onboarding')->default(false);
            $table->string('device_token')->nullable();
            $table->string('telegram', 200);
            $table->string('vk', 200);
            $table->string('twitch', 200);
            $table->string('discord', 200);
            $table->string('reddit', 200);
            $table->string('spotify', 200);
            $table->boolean('posts_privacy')->default(true);
            $table->string('document_id', 100);
            $table->string('crypto_wallet');
            $table->string('threads', 200);
            $table->enum('allow_live_streaming_private', ['on', 'off'])->default('off');
            $table->decimal('price_live_streaming_private', 10, 2)->nullable();
            $table->boolean('allow_dm')->default(true);
            $table->text('welcome_message_new_subs');
            $table->boolean('send_welcome_message')->default(false);
            $table->decimal('price_welcome_message', 10, 2)->nullable();
            $table->string('alias_mp', 100);
            $table->string('cvu', 100);
            $table->string('kick', 200);
            $table->boolean('email_new_message')->default(true);
            $table->boolean('allow_comments')->default(true);
            
            $table->index('username');
            $table->index('email');
            $table->index('countries_id');
            $table->index('role');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
