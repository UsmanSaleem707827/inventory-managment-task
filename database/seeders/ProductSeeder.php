<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\PricingRule;

class ProductSeeder extends Seeder
{
    public function run(): void
    {

        $product = Product::create([
            'sku' => 'SKU123',
            'name' => 'Laptop',
            'description' => 'Test product.',
            'base_price' => 200
        ]);

        Inventory::create([
            'product_id' => $product->id,
            'quantity' => 100,
            'location' => 'Warehouse 1',
            'cost' => 150
        ]);

        PricingRule::create([
            'product_id' => $product->id,
            'type' => 'quantity',
            'min_quantity' => 10,
            'discount' => 5
        ]);

        PricingRule::create([
            'product_id' => $product->id,
            'type' => 'time',
            'start_time' => '00:00:00',
            'end_time' => '23:59:59',
            'days_of_week' => 'Sat,Sun',
            'discount' => 10
        ]);
    }
}
