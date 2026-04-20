<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class GoldRateUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:gold-rate-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiKey = basicControl()->metal_price_api_key;
        $baseCurrency = basicControl()->base_currency;
        $currencies = 'XAU';
        $url = "https://api.metalpriceapi.com/v1/latest";

        $response = Http::get($url, [
            'api_key' => $apiKey,
            'base' => $baseCurrency,
            'currencies' => $currencies
        ]);
        if ($response->successful()) {
            $data = $response->json();
            // Gold rate in USD
            $goldRateUSD = $data['rates'][$baseCurrency.'XAU'] ?? null;

            if ($goldRateUSD !== null) {

                \App\Models\GoldPrice::create([
                    'rate' => $goldRateUSD,
                    'currency' => 'USD',
                    'recorded_at' => Carbon::now()
                ]);


            }
        }
    }
}
