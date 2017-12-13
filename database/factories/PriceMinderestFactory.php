<?php

use App\Models\Pricing\PriceMinderest;
use Faker\Generator as Faker;

$factory->define(PriceMinderest::class, function (Faker $faker) {
    return [
        'sku' => $faker->numberBetween(10000, 99999),
        'price_amazon' => $faker->randomFloat(2, 8, 90)
    ];
});
