<?php

use App\Models\Pricing\PriceAmazon;
use Tests\FeatureTestCase;

class PriceAmazonTest extends FeatureTestCase
{
    protected $product;
    protected $product2;
    protected $product3;

    protected $price;
    protected $price2;
    protected $price3;

    function setUp()
    {
        parent::setUp();

        $this->product = $this->createProduct([
            'weight' => 1,
            'tax_class_id' => 6
        ]);

        $this->price = $this->createPrice([
            'sku' => $this->product->sku,
            'pnn' => 12,
            'pvp' => 20,
            'margin_e' => 2
        ]);

        $this->product2 = $this->createProduct([
            'weight' => 12,
            'tax_class_id' => 5
        ]);

        $this->price2 = $this->createPrice([
            'sku' => $this->product2->sku,
            'pnn' => 20,
            'pvp' => 75,
            'margin_e' => 2
        ]);

        $this->product3 = $this->createProduct([
            'weight' => 32,
            'tax_class_id' => 6
        ]);

        $this->price3 = $this->createPrice([
            'sku' => $this->product3->sku,
            'pnn' => 16,
            'pvp' => 20,
            'margin_e' => 2
        ]);
    }

    function test_can_get_shipping_cost_from_shipping_cost_range_array()
    {
        $priceAmazon = new PriceAmazon($this->price);

        $this->assertSame(3.81, $priceAmazon->shipping_cost_weight());

        $priceAmazon2 = new PriceAmazon($this->price2);

        $this->assertSame(5.37, $priceAmazon2->shipping_cost_weight());

        $priceAmazon3 = new PriceAmazon($this->price3);

        $this->assertSame(8.18, $priceAmazon3->shipping_cost_weight());
    }

    function test_calculate_final_shipping_cost()
    {
        $priceAmazon = new PriceAmazon($this->price);

        $this->assertSame(3.81 - 4.99, $priceAmazon->shipping_cost());

        $priceAmazon2 = new PriceAmazon($this->price2);

        $this->assertSame(5.37 - 4.99, $priceAmazon2->shipping_cost());

        $priceAmazon3 = new PriceAmazon($this->price3);

        $this->assertSame(8.18 - 4.99, $priceAmazon3->shipping_cost());
    }

    function test_calculate_margin()
    {
        $priceAmazon = new PriceAmazon($this->price);

        $this->assertSame(2 / 2, $priceAmazon->margin());

        $priceAmazon2 = new PriceAmazon($this->price2);

        $this->assertSame(2, $priceAmazon2->margin());
    }

    function test_calculate_minimum_price()
    {
        $priceAmazon = new PriceAmazon($this->price);

        $this->assertSame($this->price->pvp * 1.03, $priceAmazon->minimum_price());

        $priceAmazon3 = new PriceAmazon($this->price3);

        $result = (
                2.55 +
                $priceAmazon3->shipping_cost() +
                $this->price3->pnn +
                $priceAmazon3->margin()) * 1.15 * $priceAmazon->get_iva();

        $this->assertSame($result, $priceAmazon3->minimum_price());
    }

    function test_calculate_maximum_price()
    {
        $priceAmazon = new PriceAmazon($this->price);

        $this->assertSame($priceAmazon->minimum_price() * 1.15, $priceAmazon->maximum_price());

        $priceAmazon2 = new PriceAmazon($this->price2);

        $this->assertSame($priceAmazon2->minimum_price() * 1.10, $priceAmazon2->maximum_price());
    }

    function test_calculate_price()
    {
        $priceMinderest = $this->createPriceMinderest([
            'sku' => $this->price->sku,
            'price_amazon' => 26
        ]);

        $priceAmazon = new PriceAmazon($this->price);

        $this->assertSame(($priceMinderest->price_amazon - 0.10), $priceAmazon->price());

        $priceMinderest = $this->createPriceMinderest([
            'sku' => $this->price2->sku,
            'price_amazon' => 60
        ]);

        $priceAmazon2 = new PriceAmazon($this->price2);

        $this->assertSame($priceAmazon2->maximum_price(), $priceAmazon2->price());
    }

    function test_set_price()
    {
        $priceMinderest = $this->createPriceMinderest([
            'sku' => $this->price->sku,
            'price_amazon' => 26
        ]);

        $priceAmazon = new PriceAmazon($this->price);

        $priceAmazon->set_price();

        $this->assertSame(round($priceAmazon->price() / $priceAmazon->get_iva(), 2), $priceAmazon->pvp);

        $priceMinderest = $this->createPriceMinderest([
            'sku' => $this->price2->sku,
            'price_amazon' => 60
        ]);

        $priceAmazon2 = new PriceAmazon($this->price2);

        $priceAmazon2->set_price();

        $this->assertSame(round($priceAmazon2->price() / $priceAmazon2->get_iva(), 2), $priceAmazon2->pvp);
    }

}
