<?php

namespace App\Data;

use Homeful\Mortgage\Data\MortgageData;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Data;

class MatchData extends Data
{
    public function __construct(
        /** @var MortgageData[] */
        public ?DataCollection $mortgages,
    ) {}
}
