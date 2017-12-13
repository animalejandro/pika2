<?php

namespace App\Models\Pricing;

use Illuminate\Database\Eloquent\Model;

class PriceMinderest extends Model
{
    protected $table = 'prices_minderest';

    public function price()
    {
        return $this->belongsTo(Price::class, 'sku', 'sku');
    }
}
