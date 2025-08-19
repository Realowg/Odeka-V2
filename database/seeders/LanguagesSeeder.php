<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('languages')->insert([
        [
            'id' => 1,
            'name' => 'English',
            'abbreviation' => 'en'
        ],
        [
            'id' => 2,
            'name' => 'Español',
            'abbreviation' => 'es'
        ],
        [
            'id' => 3,
            'name' => 'Français',
            'abbreviation' => 'fr'
        ]
        ]);
    }
}