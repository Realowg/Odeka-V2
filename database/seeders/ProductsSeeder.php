<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
        [
            'id' => 1,
            'user_id' => 2,
            'name' => 'MasterDish',
            'type' => 'physical',
            'price' => 5000.00,
            'delivery_time' => 0,
            'country_free_shipping' => '214',
            'tags' => 'Concours, Évènement',
            'description' => 'Desc for the product',
            'file' => '',
            'mime' => null,
            'extension' => null,
            'size' => null,
            'status' => '1',
            'created_at' => '2025-05-18 22:08:57',
            'updated_at' => '2025-05-18 22:08:57',
            'shipping_fee' => 500.00,
            'quantity' => 10,
            'box_contents' => 'Multiple 34 things',
            'category' => '1',
            'downloads' => 0,
            'external_link' => 'https://odeka.org'
        ]
        ]);
    }
}