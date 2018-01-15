<?php

namespace App\Models\Pricing;

use Illuminate\Database\Eloquent\Model;

class PriceAmazon extends Model
{
    protected $table = 'prices_amazon';

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
    protected static $comission = 1.18;

    // Maximum price increment
    protected static $maximum_price_range = [
        10 => 1.5,
        20 => 1.3,
        40 => 1.15,
        50 => 1.10
    ];

    protected $price;

    //---------------------------------------------------------------------------------------------------------
    // Methods
    //---------------------------------------------------------------------------------------------------------

    public function __construct(Price $price, array $attributes = [])
    {
        parent::__construct($attributes);

        $this->price = $price;
    }

    // Cálculo del PVP final
    public function price()
    {
        if ( isset($this->price->minderest->price_amazon) ) {
            if (($this->minimum_price() - 0.05 + static::$customer_shipping_cost) < $this->price->minderest->price_amazon) {
                return $this->price->minderest->price_amazon - 0.05;
            }
        }

        return $this->maximum_price();
    }

    // Actualiza el precio (sin IVA) del producto
    public function set_price()
    {
        $this->sku = $this->price->product->sku;
        $this->pnn = $this->price->pnn;
        $this->pvp = round($this->price() / $this->get_iva(), 2);

        $this->save();
    }

    // Set IVA value
    public function set_iva()
    {
        return ($this->price->product->tax_class_id == 5) ? 1.21 : 1.1;
    }

    // Get IVA value
    public function get_iva()
    {
        return $this->set_iva();
    }

    // Cálculo del Coste de envío según su peso
    public function shipping_cost_weight()
    {
        foreach (static::$shipping_cost_range as $w => $c) {
            if ($this->price->product->weight <= $w) {
                return static::$shipping_cost_range[$w];
            }
            // if weight > 30 => shipping_cost = $last_cost (from last array item)
            if (end(static::$shipping_cost_range) == $c) {
                return end(static::$shipping_cost_range);
            }
        }
    }

    // Coste de envío final
    public function shipping_cost()
    {
        return $this->shipping_cost_weight() - static::$customer_shipping_cost;
    }

    // Cálculo del margen a añadir al precio en Amazon
    public function margin()
    {
        if ($this->price->margin_e >= $this->price->pvp * 0.05) {
            return $this->price->margin_e / 2;
        }

        return $this->price->margin_e;
    }

    // Cálculo del PVP mínimo Amazon
    public function minimum_price()
    {
        $minimum_price = (
                static::$logistic_cost +
                $this->shipping_cost() +
                $this->price->pnn +
                $this->margin()
            ) * static::$comission * $this->get_iva();

        if ($minimum_price < $this->price->pvp)
                return $this->price->pvp * 1.03;

        return $minimum_price;
    }

    // Cálculo del PVP máximo Amazon
    public function maximum_price()
    {
        foreach (static::$maximum_price_range as $price => $increment) {
            if ($this->minimum_price() <= $price) {
                return $this->minimum_price() * static::$maximum_price_range[$price];
            }
            // if $price > 50 => maximum_price = minimum_price * last_increment
            if (end(static::$maximum_price_range) == $increment) {
                return $this->minimum_price() * end(static::$maximum_price_range);
            }
        }
    }
}
