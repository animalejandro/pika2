<?php

namespace App\Http\Controllers\Pricing;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\Pricing\{Price, PriceAmazon};
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class PriceAmazonController extends Controller
{
    public function generatePrices()
    {
        DB::table('prices_amazon')->truncate();

        $prices = Price::all();

        foreach ($prices as $price) {

            $amazonPrice = new PriceAmazon($price);

            $amazonPrice->set_price();
        }
    }

    public function exportToCSV($filename, $path)
    {
        Excel::create($filename, function($excel) {

            $excel->sheet('amazon-prices', function($sheet) {

                $sheet->row(1, array(
                    'REF',
                    'PVPR',
                    'PVP',
                    'PCP',
                    'TARIFA',
                    'OFERTA',
                ));

                $pricesAmazon = DB::table('prices_amazon')->get();

                foreach($pricesAmazon as $price)
                {
                    $sheet->appendRow([
                        $price->sku, 1,
                        $price->pvp, 1,
                        $price->pnn,
                        'NO',
                    ]);
                }
            });
        })->store('csv', $path);

        $this->uploadToFTP($filename . '.csv', $path);
    }

    // Upload file to Animazon FTP
    protected function uploadToFTP($file, $path)
    {
        Storage::disk('ftp_pricing_amazon')->put($file, file_get_contents($path . $file));
    }

}