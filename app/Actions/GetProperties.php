<?php

namespace App\Actions;

use Homeful\Property\Property;
use Lorisleiva\Actions\Concerns\AsAction;

class GetProperties
{
    use AsAction;

    public function handle()
    {
        return [
            (new Property)->setTotalContractPrice(750000),
            (new Property)->setTotalContractPrice(800000),
            (new Property)->setTotalContractPrice(1000000),
            (new Property)->setTotalContractPrice(1500000),
            (new Property)->setTotalContractPrice(2000000),
            (new Property)->setTotalContractPrice(2500000),
            (new Property)->setTotalContractPrice(2707500),
        ];
    }
}
