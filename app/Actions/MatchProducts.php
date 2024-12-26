<?php

namespace App\Actions;

use Homeful\Borrower\Exceptions\{MaximumBorrowingAgeBreached, MinimumBorrowingAgeNotMet};
use Brick\Math\Exception\{NumberFormatException, RoundingNecessaryException};
use Brick\Money\Exception\UnknownCurrencyException;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\ActionRequest;
use Homeful\Common\Classes\Amount;
use Homeful\Borrower\Borrower;
use Illuminate\Support\Carbon;
use Homeful\Mortgage\Mortgage;
use App\Data\MatchData;

class MatchProducts
{
    use AsAction;

    protected MatchData $data;

    /**
     * @throws MaximumBorrowingAgeBreached
     * @throws RoundingNecessaryException
     * @throws MinimumBorrowingAgeNotMet
     * @throws UnknownCurrencyException
     * @throws NumberFormatException
     */
    public function handle(array $validated): MatchData
    {
        $borrower = (new Borrower)
            ->setBirthdate(Carbon::parse($validated['date_of_birth']))
            ->setGrossMonthlyIncome($validated['monthly_gross_income'])
            ->setRegional($validated['region'] != 'NCR');

        $properties = GetProperties::run();

        $key = $validated['date_of_birth'] . '_' . $validated['monthly_gross_income'] . '_' . $validated['region'];
        $mortgages = Cache::remember($key, now()->addMinutes(5), function() use ($properties, $borrower, $key) {
            logger($key);
            logger(now());
            $mortgages = [];
            foreach ($properties as $property) {
                $mortgage = new Mortgage($property, $borrower, []);
                if ($this->match($mortgage))
                    $mortgages[] = $mortgage;
            }
            return $mortgages;
        });

        $matches = ['matches' => $mortgages];

        return MatchData::from($matches)->only(...$this->getFilter());
    }

    public function rules(): array
    {
        return [
            'date_of_birth' => ['required', 'date'],
            'monthly_gross_income' => ['required', 'numeric', 'min:0'],
            'region' => ['required', 'string', 'max:100'],
        ];
    }

    /**
     * @throws MinimumBorrowingAgeNotMet
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     * @throws NumberFormatException
     * @throws MaximumBorrowingAgeBreached
     */
    public function asController(ActionRequest $request): void
    {
        $this->data = $this->handle($request->validated());
    }

    public function htmlResponse(): \Illuminate\Http\RedirectResponse
    {
        return back()->with('event', [
            'name' => 'match',
            'data' => $this->data,
        ]);
    }

    public function jsonResponse(): MatchData
    {
        return $this->data;
    }

    protected function match(Mortgage $mortgage): bool
    {
        return $mortgage->getJointBorrowerDisposableMonthlyIncome()->compareTo($mortgage->getLoan()->getMonthlyAmortization()) == Amount::GREATER_THAN;
    }

    protected function getFilter(): array
    {
        return [
            'matches.borrower.age',
            'matches.borrower.maximum_term_allowed',
            'matches.borrower.repricing_frequency',
            'matches.borrower.interest_rate',
            'matches.property.sku',
            'matches.property.total_contract_price',
            'matches.property.appraised_value',
            'matches.percent_down_payment',
            'matches.dp_term',
            'matches.bp_interest_rate',
            'matches.percent_mf',
            'matches.bp_term',
            'matches.miscellaneous_fees',
            'matches.down_payment',
            'matches.cash_out',
            'matches.dp_amortization',
            'matches.loan_amount',
            'matches.loan_amortization',
            'matches.partial_miscellaneous_fees',
            'matches.income_requirement_multiplier',
            'matches.joint_disposable_monthly_income',
            'matches.income_requirement',
            'matches.present_value_from_monthly_disposable_income',
            'matches.loan_difference'
        ];
    }
}