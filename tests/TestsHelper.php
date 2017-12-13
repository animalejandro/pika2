<?php

namespace Tests;

use App\Models\Pricing\{Price, PriceAmazon, PriceMinderest};
use App\Models\Catalog\Product;

trait TestsHelper
{
    protected function createProduct(array $attributes = [])
    {
        return factory(Product::class)->create($attributes);
    }

    protected function createPrice(array $attributes = [])
    {
        return factory(Price::class)->create($attributes);
    }

    protected function createPriceMinderest(array $attributes = [])
    {
        return factory(PriceMinderest::class)->create($attributes);
    }

}