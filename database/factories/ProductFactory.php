<?php

use Faker\Generator as Faker;

use App\Models\Catalog\Product;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'sku' => $faker->numberBetween(10000, 99999),
        'weight' => $faker->randomFloat(2, 0.5, 30),
        'tax_class_id' => $faker->randomElement([5, 6])
    ];
});
