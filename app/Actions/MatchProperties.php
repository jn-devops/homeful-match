<?php

namespace App\Actions;

use Homeful\Borrower\Exceptions\{MaximumBorrowingAgeBreached, MinimumBorrowingAgeNotMet};
use Brick\Math\Exception\{NumberFormatException, RoundingNecessaryException};
use Homeful\Payment\Exceptions\{MaxCycleBreached, MinTermBreached};
use Brick\Money\Exception\UnknownCurrencyException;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\Common\Classes\Amount;
use Homeful\Borrower\Borrower;
use Illuminate\Support\Carbon;
use Homeful\Mortgage\Mortgage;
use App\Data\MatchData;

class MatchProperties
{
    use AsAction;

    /**
     * @throws MaximumBorrowingAgeBreached
     * @throws RoundingNecessaryException
     * @throws MinimumBorrowingAgeNotMet
     * @throws UnknownCurrencyException
     * @throws NumberFormatException
     * @throws MaxCycleBreached
     * @throws MinTermBreached
     */
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
            if ($this->match($mortgage))
                $mortgages[] = $mortgage;
        }
        $mortgages = ['mortgages' => $mortgages];

        return MatchData::from($mortgages);
    }

    protected function match(Mortgage $mortgage): bool
    {
        return $mortgage->getJointBorrowerDisposableMonthlyIncome()->compareTo($mortgage->getLoan()->getMonthlyAmortization()) == Amount::GREATER_THAN;
    }
}
