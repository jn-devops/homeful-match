<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\Common\Classes\Amount;
use Homeful\Borrower\Borrower;
use Illuminate\Support\Carbon;
use Homeful\Mortgage\Mortgage;
use App\Data\MatchData;

class MatchProperties
{
    use AsAction;

    public function handle(array $validated): MatchData
    {
        $borrower = (new Borrower)
            ->setBirthdate(Carbon::parse($validated['date_of_birth']))
            ->setGrossMonthlyIncome($validated['monthly_gross_income'])
            ->setRegional($validated['monthly_gross_income'] != 'NCR');

        $mortgages = [];
        $properties = GetProperties::run();
        foreach ($properties as $property) {
            $mortgage = new Mortgage($property, $borrower, []);
            if ($mortgage->getLoanDifference()->compareTo(0) == Amount::LESS_THAN)
                $mortgages[] = $mortgage;
        }
        $mortgages = ['mortgages' => $mortgages];

        return MatchData::from($mortgages);
    }
}
