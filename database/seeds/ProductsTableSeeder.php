<?php

use App\Models\Catalog\Product;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        factory(Product::class, 50)->create();
    }
}
