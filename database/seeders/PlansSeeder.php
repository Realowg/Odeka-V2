<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('plans')->insert([
        [
            'id' => 1,
            'user_id' => 4,
            'name' => 'user_4_weekly',
            'price' => 200.00,
            'interval' => 'weekly',
            'paystack' => '',
            'status' => '0',
            'created_at' => '2025-05-22 03:57:29',
            'updated_at' => '2025-05-22 03:57:29'
        ],
        [
            'id' => 2,
            'user_id' => 4,
            'name' => 'user_4',
            'price' => 400.00,
            'interval' => 'monthly',
            'paystack' => '',
            'status' => '1',
            'created_at' => '2025-05-22 03:57:29',
            'updated_at' => '2025-05-22 03:57:29'
        ],
        [
            'id' => 3,
            'user_id' => 4,
            'name' => 'user_4_quarterly',
            'price' => 10000.00,
            'interval' => 'quarterly',
            'paystack' => '',
            'status' => '1',
            'created_at' => '2025-05-22 03:57:29',
            'updated_at' => '2025-05-22 03:57:29'
        ],
        [
            'id' => 4,
            'user_id' => 4,
            'name' => 'user_4_biannually',
            'price' => 10000.00,
            'interval' => 'biannually',
            'paystack' => '',
            'status' => '1',
            'created_at' => '2025-05-22 03:57:29',
            'updated_at' => '2025-05-22 03:57:29'
        ],
        [
            'id' => 5,
            'user_id' => 4,
            'name' => 'user_4_yearly',
            'price' => 10000.00,
            'interval' => 'yearly',
            'paystack' => '',
            'status' => '1',
            'created_at' => '2025-05-22 03:57:29',
            'updated_at' => '2025-05-22 03:57:29'
        ]
        ]);
    }
}