<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payment_gateways')) {
            return;
        }

        $data = [
            'name' => 'Kkiapay',
            'type' => 'normal',
            'enabled' => '1',
            'sandbox' => env('KKIAPAY_SANDBOX', true) ? 'true' : 'false',
            'fee' => 0.0,
            'fee_cents' => 0.00,
            'email' => '',
            'token' => Str::random(40),
            'key' => env('KKIAPAY_PUBLIC_KEY', ''),
            'key_secret' => env('KKIAPAY_PRIVATE_KEY', ''),
            'bank_info' => '',
            'recurrent' => 'no',
            'logo' => '',
            'webhook_secret' => env('KKIAPAY_SECRET', ''),
            'subscription' => 'no',
            'ccbill_accnum' => '',
            'ccbill_subacc' => '',
            'ccbill_flexid' => '',
            'ccbill_salt' => '',
            'ccbill_subacc_subscriptions' => '',
            'project_id' => null,
            'project_secret' => null,
            'ccbill_datalink_username' => null,
            'ccbill_datalink_password' => null,
            'ccbill_skip_subaccount_cancellations' => 0,
            'allow_payments_alipay' => 0,
            'crypto_currency' => null,
        ];

        $existing = DB::table('payment_gateways')->where('name', 'Kkiapay')->first();
        if ($existing) {
            DB::table('payment_gateways')->where('id', $existing->id)->update($data);
        } else {
            DB::table('payment_gateways')->insert($data);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('payment_gateways')) {
            DB::table('payment_gateways')->where('name', 'Kkiapay')->delete();
        }
    }
};


