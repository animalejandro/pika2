<?php

namespace App\Models\Pricing;

use Illuminate\Database\Eloquent\Model;

class PriceAmazon extends Model
{
    public $table = 'prices_amazon';

    /* Relationship
    public function product()
    {
        return $this->belongsTo('App\Models\Catalog\Product', 'sku', 'sku');
    }

    public function minderest()
    {
        return $this->belongsTo('App\Models\Pricing\PriceAmazonMinderest', 'sku', 'sku');
    }
    */

    //---------------------------------------------------------------------------------------------------------
    // Variables
    //---------------------------------------------------------------------------------------------------------

    // Coste logístico
    protected static $logistic_cost = 2.55;

    // Weight / Cost relation
    protected static $shipping_cost_range = [
        2 => 3.81,
        5 => 4.27,
        10 => 4.78,
        15 => 5.37,
        20 => 6.31,
        25 => 7.25,
        30 => 8.18
    ];

    // Costes de envío pagado por el cliente
    protected static $customer_shipping_cost = 4.99;

    // Comisión añadida a cada producto vendida por Amazon
    protected static $comission = 1.15;

    // Maximum price increment
    protected static $maximum_price_range = [
        10 => 1.5,
        20 => 1.3,
        40 => 1.15,
        50 => 1.10
    ];

    protected $weight;

    //---------------------------------------------------------------------------------------------------------
    // Methods
    //---------------------------------------------------------------------------------------------------------

    // Cálculo del Coste de envío según su peso
    public static function weight_shipping_cost($weight)
    {
        foreach (static::$shipping_cost_range as $w => $c) {
            if ($weight <= $w) {
                return static::$shipping_cost_range[$w];
            }
            // if weight > 30 => shipping_cost = $last_cost (from last array item)
            if (end(static::$shipping_cost_range) == $c) {
                return end(static::$shipping_cost_range);
            }
        }
    }

    // Coste de envío final
    protected static function shipping_cost($weight)
    {
        return static::weight_shipping_cost($weight) - static::$customer_shipping_cost;
    }

    // Cálculo del margen a añadir al precio en Amazon
    protected static function margin($animalear_margin, $animalear_pvp)
    {
        if ($animalear_margin >= $animalear_pvp * 0.05) {
            return $animalear_margin / 2;
        }

        return $animalear_margin;
    }

    // Cálculo del PVP mínimo Amazon
    protected static function minimum_price($shipping_cost, $pnn, $margin, $iva, $animalear_price)
    {
        $minimum_price = (
                static::$logistic_cost +
                $shipping_cost +
                $pnn +
                $margin
        ) * static::$comission * (1 + $iva);

        if ($minimum_price < $animalear_price) {
            return $animalear_price * 1.03;
        }

        return $minimum_price;
    }

    // Cálculo del PVP máximo Amazon
    protected static function maximum_price($minimum_price)
    {
        foreach (static::$maximum_price_range as $price => $increment) {
            if ($minimum_price <= $price) {
                return $minimum_price * static::$maximum_price_range[$price];
            }
            // if $price > 50 => maximum_price = minimum_price * last_increment
            if (end(static::$maximum_price_range) == $increment) {
                return $minimum_price * end(static::$maximum_price_range);
            }
        }
    }

    // Cálculo del PVP final
    protected static function final_price($minimum_price, $maximum_price, $buy_box_price)
    {
        if (($minimum_price - 0.10 + static::$customer_shipping_cost) < $buy_box_price) {
            return $buy_box_price - 0.10;
        }

        return $maximum_price;
    }

    // Actualiza el precio (sin IVA) del producto
    public function set_final_price($pnn, $minimum_price, $maximum_price, $buy_box_price, $iva)
    {
        $price = static::final_price($minimum_price, $maximum_price, $buy_box_price);

        $this->pnn = $pnn;

        $this->pvp = round($price / (1 + $iva), 2);

        $this->save();
    }

    // Actualiza todos los Precios de Amazon
    protected function update_prices()
    {
        $amazon_prices = PriceAmazon::all();

        foreach ($amazon_prices as $amazonPrice) {

            $minimum_price = $amazonPrice::minimum_price(
                    $amazonPrice::shipping_cost($amazonPrice->product->weight),
                    $amazonPrice->product->price->pnn,
                    $amazonPrice::margin(
                            $amazonPrice->product->price->margen_estimado,
                            $amazonPrice->product->price->pvp
                    ),
                    ($amazonPrice->product->tax_class_id == 5) ? 0.21 : 0.1,
                    $amazonPrice->product->price->pvp
            );

            $maximum_price = $amazonPrice::maximum_price($minimum_price);

            $amazonPrice->set_final_price(
                    $amazonPrice->product->price->pnn,
                    $minimum_price,
                    $maximum_price,
                    $amazonPrice->minderest->price,
                    ($amazonPrice->product->tax_class_id == 5) ? 0.21 : 0.1
            );
        }
    }

}
