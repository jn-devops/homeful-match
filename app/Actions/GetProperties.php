<?php

namespace App\Actions;

use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Money\Exception\MoneyMismatchException;
use Brick\Money\Exception\UnknownCurrencyException;
use Illuminate\Support\Facades\{Cache, Http};
use Homeful\Property\Exceptions\MaximumContractPriceBreached;
use Homeful\Property\Exceptions\MinimumContractPriceBreached;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\Property\Property;

class GetProperties
{
    use AsAction;

    public function handle()
    {

        return Cache::remember('properties', now()->addMinutes(5), function() {
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
