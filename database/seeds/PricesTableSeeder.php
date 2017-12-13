<?php

use App\Models\Catalog\Product;
use App\Models\Pricing\Price;
use Illuminate\Database\Seeder;

class PricesTableSeeder extends Seeder
{
    public function run()
    {
        $products = Product::all();

        foreach($products as $product) {
            factory(Price::class)->create([
                'sku' => $product->sku
            ]);
        }

    }
}
