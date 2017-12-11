<?php

use App\Models\Pricing\PriceAmazon;
use Tests\FeatureTestCase;

class PriceAmazonTest extends FeatureTestCase
{
    function test_can_get_shipping_cost_from_shipping_cost_range_array()
    {
        $shipping_cost = PriceAmazon::weight_shipping_cost($weight = 0.5);

        $this->assertSame(3.81, $shipping_cost);

        $shipping_cost = PriceAmazon::weight_shipping_cost($weight = 12);

        $this->assertSame(5.37, $shipping_cost);

        $shipping_cost = PriceAmazon::weight_shipping_cost($weight = 32);

        $this->assertSame(8.18, $shipping_cost);
    }

    function test_calculate_final_shipping_cost()
    {
        $shipping_cost = PriceAmazon::shipping_cost($weight = 0.5);

        $this->assertSame(3.81 - 4.99, $shipping_cost);

        $shipping_cost = PriceAmazon::shipping_cost($weight = 12);

        $this->assertSame(5.37 - 4.99, $shipping_cost);

        $shipping_cost = PriceAmazon::shipping_cost($weight = 32);

        $this->assertSame(8.18 - 4.99, $shipping_cost);
    }

    function test_calculate_margin()
    {
        $margin = PriceAmazon::margin($animalear_margin = 2, $animalear_price = 20);

        $this->assertSame(2 / 2, $margin);

        $margin2 = PriceAmazon::margin($animalear_margin = 2, $animalear_price = 75);

        $this->assertSame(2, $margin2);
    }

    function test_calculate_minimum_price()
    {
        $minimum_price = PriceAmazon::minimum_price($shipping_cost = -0.72,  $pnn = 10, $margin = 1, $iva = 0.1);

        $this->assertSame(16.22995, $minimum_price);

        $minimum_price = PriceAmazon::minimum_price($shipping_cost = -0.72,  $pnn = 10, $margin = 1, $iva = 0.21);

        $this->assertSame(17.852945, $minimum_price);
    }

    function test_check_if_minimum_price_is_lower_than_animalear_price()
    {
        $minimum_price = PriceAmazon::minimum_price_exception($animalear_price = 25, $minimum_price = 24.2);

        $this->assertSame(25.75, $minimum_price);

        $minimum_price = PriceAmazon::minimum_price_exception($animalear_price = 25, $minimum_price = 28.6);

        $this->assertSame(28.6, $minimum_price);
    }

    function test_calculate_maximum_price()
    {
        $maximum_price = PriceAmazon::maximum_price($minimum_price = 6);

        $this->assertSame(9.0, $maximum_price);

        $maximum_price = PriceAmazon::maximum_price($minimum_price = 15);

        $this->assertSame(19.5, $maximum_price);

        $maximum_price = PriceAmazon::maximum_price($minimum_price = 28);

        $this->assertSame(32.2, $maximum_price);

        $maximum_price = PriceAmazon::maximum_price($minimum_price = 70);

        $this->assertSame(77.0, $maximum_price);
    }

    function test_calculate_final_price()
    {
        $price_amazon = $this->createPriceAmazon();

        $price_amazon->set_final_price($minimum_price = 20, $maximum_price = 28, $buy_box_price = 26, $iva = 0.10);

        $this->assertSame(23.55, $price_amazon->pvp);

        $price_amazon->set_final_price($minimum_price = 22, $maximum_price = 28, $buy_box_price = 25, $iva = 0.21);

        $this->assertSame(23.14, $price_amazon->pvp);
    }

}
