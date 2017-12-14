<?php

use App\Http\Controllers\Pricing\PriceAmazonController;

use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('prices:amazon', function ( PriceAmazonController $priceAmazonController ) {
    $priceAmazonController->generatePrices();
    $priceAmazonController->exportToCSV(
            Carbon::tomorrow()->format('Ymd').'0000-update-preus',  // Filename
            storage_path('pricing/amazon/')                         // Path
    );
})->describe('Generar precios para Amazon');