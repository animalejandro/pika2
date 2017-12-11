<?php

namespace Tests;

use App\Models\Pricing\{PriceAmazon, PriceMinimumAmazon};

trait TestsHelper
{
    /*
    protected function createPriceMinimumAmazon(array $attributes = [])
    {
        return factory(PriceMinimumAmazon::class)->create($attributes);
    }
    */

    protected function createPriceAmazon(array $attributes = [])
    {
        return factory(PriceAmazon::class)->create($attributes);
    }
}