<?php

use App\Models\Pricing\Price;
use App\Models\Pricing\PriceMinderest;
use Illuminate\Database\Seeder;

class PricesMinderestTableSeeder extends Seeder
{
    public function run()
    {
        $prices = Price::all();

        foreach($prices as $price) {
            factory(PriceMinderest::class)->create([
                'sku' => $price->sku,
                'price_amazon' => round($price->pvp * 1.20, 2)
            ]);
        }

    }
}
