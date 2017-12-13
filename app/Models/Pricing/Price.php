<?php

namespace App\Models\Pricing;

use Illuminate\Database\Eloquent\Model;
use App\Models\Catalog\Product;

class Price extends Model
{
    public function product()
    {
        return $this->belongsTo(Product::class, 'sku', 'sku');
    }

    public function minderest()
    {
        return $this->hasOne(PriceMinderest::class, 'sku', 'sku');
    }

}
