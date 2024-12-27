<?php

namespace App\Actions;

use Homeful\Property\Exceptions\MinimumContractPriceBreached;
use Illuminate\Support\Facades\{Cache, Http};
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\Property\Property;

class GetProperties
{
    use AsAction;

    public function handle()
    {
        return Cache::remember('properties', now()->addMinutes(config('homeful-match.cache.ttl')), function() {
            logger('properties');
            logger(now());
            $response = Http::get('https://properties.homeful.ph/fetch-products');
            $properties = [];
            foreach ($response->json('products') as $record) {
                try {
                    $properties [] = (new Property)
                        ->setSKU($record['sku'])
                        ->setTotalContractPrice($record['price'])
                        ->setAppraisedValue($record['appraised_value'])
                        ->setPercentDownPayment($record['percent_down_payment'])
                        ->setDownPaymentTerm($record['down_payment_term'])
                        ->setPercentMiscellaneousFees($record['percent_miscellaneous_fees'])
                    ;
                }
                catch (MinimumContractPriceBreached $exception) {

                }
            }

            return $properties;
        });
    }
}
