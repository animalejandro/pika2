<?php

use Faker\Generator as Faker;

use App\Models\Pricing\Price;

$factory->define(Price::class, function (Faker $faker) {
    return [
        'sku' => $faker->numberBetween(10000, 99999),
        'pnn' => $pnn = $faker->randomFloat(2, 5, 80),
        'pvp' => $pnn * 1.6,
        'margin_e' => $faker->randomFloat(2, 1, 5),
    ];
});
