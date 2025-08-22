<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('product_categories')->insert([
  
  [
    'id' => 1,
    'name' => 'Mode et accessoires',
    'description' => 'Vêtements, bijoux, sacs à main, chaussures et autres articles liés à la mode.',
    'keywords' => 'Vêtements,Bijoux,Sacs à main,Chaussures,Mode',
    'mode' => 'on',
    'image' => 'Models-YnDCrsJQ1UtBTpXOFFFpwnDpFyqAWcU1.png',
  ],
  
  [
    'id' => 0,
    'name' => 'Test',
    'description' => 'Test',
    'keywords' => 'Test',
    'mode' => 'on',
    'image' => '-P06YPG6O00zHmR9npMEMwx0kQywVHyEv.png',
  ],
  
  [
    'id' => 0,
    'name' => 'Fact',
    'description' => 'Fact',
    'keywords' => 'Fact',
    'mode' => 'on',
    'image' => '-OthIrRQ9sqEhEHYdKOa2kRUISSMWQaNK.png',
  ],
]);
    }
}