<?php

namespace App\Http\Controllers\Pricing;

use App\Http\Controllers\Controller;
use App\Models\Pricing\Price;
use App\Models\Pricing\PriceAmazon;

class PriceAmazonController extends Controller
{
    public function generate()
    {
        //PriceAmazon::truncate();

        $prices = Price::all();

        foreach ($prices as $price) {

            $amazonPrice = new PriceAmazon($price);

            $amazonPrice->set_price();
        }
    }
}