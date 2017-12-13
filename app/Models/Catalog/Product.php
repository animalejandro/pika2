<?php

namespace App\Models\Catalog;

use App\Models\Pricing\Price;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function price()
    {
        $this->hasOne(Price::class, 'sku', 'sku');
    }
}
