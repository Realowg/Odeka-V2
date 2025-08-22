<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(CountriesSeeder::class);
        // $this->call(LanguagesSeeder::class);
        // $this->call(LoginSessionsSeeder::class);
        // $this->call(MediaSeeder::class);
        // $this->call(MediaProductsSeeder::class);
        // $this->call(MediaStoriesSeeder::class);
        // $this->call(MediaWelcomeMessagesSeeder::class);
        // $this->call(PaymentGatewaysSeeder::class);
        // $this->call(PlansSeeder::class);
        // $this->call(ProductsSeeder::class);
        $this->call(AdminSettingsSeeder::class);
    }
}