<?php

use Faker\Generator as Faker;

use App\Models\Pricing\PriceAmazon;

$factory->define(PriceAmazon::class, function (Faker $faker) {
    return [
        'sku' => $faker->numberBetween(10000, 99999),
        'pnn' => $pnn = $faker->randomFloat(2, 5, 80),
        'pvp' => $pnn * 1.6
    ];
});
