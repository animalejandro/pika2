<?php

namespace Tests;

use App\Models\Pricing\{PriceAmazon, PriceMinimumAmazon};

trait TestsHelper
{
    protected function createPriceAmazon(array $attributes = [])
    {
        return factory(PriceAmazon::class)->create($attributes);
    }
}