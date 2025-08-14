<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['sku' => 'SKU-1001', 'name' => 'Camiseta Básica',     'stock' => 120, 'price' => 9.90,  'provider' => 'external', 'external_id' => 'p_1001'],
            ['sku' => 'SKU-1002', 'name' => 'Polo Premium',        'stock' => 80,  'price' => 19.90, 'provider' => 'external', 'external_id' => 'p_1002'],
            ['sku' => 'SKU-2001', 'name' => 'Zapatillas Urbanas',  'stock' => 45,  'price' => 49.90, 'provider' => 'external', 'external_id' => 'p_2001'],
            ['sku' => 'SKU-3001', 'name' => 'Gorro Invierno',      'stock' => 200, 'price' => 7.50,  'provider' => 'external', 'external_id' => 'p_3001'],
            ['sku' => 'SKU-4001', 'name' => 'Mochila Deportiva',   'stock' => 30,  'price' => 34.90, 'provider' => 'external', 'external_id' => 'p_4001'],
        ];

        foreach ($rows as $r) {
            Product::updateOrCreate(
                ['provider' => $r['provider'], 'external_id' => $r['external_id']],
                $r
            );
        }
    }
}
