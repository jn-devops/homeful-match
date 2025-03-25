<?php

namespace App\Actions;

use Homeful\Payment\Class\Term;
use Homeful\Payment\Enums\Cycle;
use Homeful\Payment\PresentValue;
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
use Illuminate\Support\Arr;
use App\Data\MatchData;

class MatchProducts
{
    use AsAction;

    protected MatchData $data;

    protected int $limit;
    protected int $verbose;

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
            ->setOverrideMaximumPayingAge(config('homeful-match.override_maximum_paying_age'))
            ->setRegional($validated['region'] != 'NCR');

        $properties = GetProperties::run();

        $key = $validated['date_of_birth'] . '_' . $validated['monthly_gross_income'] . '_' . $validated['region'];
        $params = config('homeful-match.params');
        $mortgages = Cache::remember($key, now()->addMinutes(config('homeful-match.cache.ttl')), function() use ($properties, $borrower, $key, $params) {
            logger($key);
            logger(now());
            $mortgages = [];
            foreach ($properties as $property) {
                $mortgage = new Mortgage($property, $borrower, $params);
                if ($this->match($mortgage))
                    $mortgages[] = $mortgage;
            }
            return $mortgages;
        });

        $matches = ['matches' => $mortgages];

        return MatchData::from($matches)->only(...$this->getFilter());
    }

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            'date_of_birth' => ['required', 'date'],
            'monthly_gross_income' => ['required', 'numeric', 'min:0'],
            'region' => ['required', 'string', 'max:100'],
            'limit' => ['nullable', 'int', 'min:1'],
            'verbose' => ['nullable', 'int', 'min:0', 'max:2'],
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
        $validated = $request->validated();
        $this->limit = Arr::pull($validated, 'limit', config('homeful-match.matches.limit', 10000));
        $this->verbose = Arr::pull($validated, 'verbose', config('homeful-match.matches.verbose', 0));
        $this->data = $this->handle($validated);
    }

    /**
     * The controller returns the
     * filtered resultant data
     * via Inertia messaging.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function htmlResponse(): \Illuminate\Http\RedirectResponse
    {
        return back()->with('event', [
            'name' => 'match',
            'data' => $this->transform($this->data, $this->limit, $this->verbose),
        ]);
    }

    /**
     * The controller returns a json response
     * if the source requests for a json.
     *
     * @return array
     */
    public function jsonResponse(): array
    {
        return $this->transform($this->data, $this->limit, $this->verbose);
    }

    /**
     * This is the matching algorithm. it compares the monthly
     * amortization of the specific product against the joint
     * disposable monthly income of the borrower - to check
     * if the borrower can afford the unit.
     *
     * @param Mortgage $mortgage
     * @return bool
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     * @throws \Homeful\Payment\Exceptions\MaxCycleBreached
     * @throws \Homeful\Payment\Exceptions\MinTermBreached
     */
    protected function match(Mortgage $mortgage): bool
    {
        $property = $mortgage->getProperty();
        $borrower = $mortgage->getBorrower();

        $disposable_income = $borrower->getMonthlyDisposableIncome()->inclusive()->getAmount()->toFloat();
        $tern = $borrower->getMaximumTermAllowed();
        $interest_rate = $property->getDefaultAnnualInterestRateFromBorrower($borrower);

        $present_value = (new PresentValue)
            ->setPayment($disposable_income)
            ->setTerm(new Term($tern, Cycle::Yearly))
            ->setInterestRate($interest_rate);
        $value = $present_value->getDiscountedValue();

        $tcp = $property->getTotalContractPrice()->inclusive()->getAmount()->toFloat();

        return $value >= $tcp;

//        return $mortgage->getJointBorrowerDisposableMonthlyIncome()->compareTo($mortgage->getLoan()->getMonthlyAmortization()) == Amount::GREATER_THAN;
    }

    /**
     * This is a list of fields that
     * are exposed by the results
     * of the matching.
     *
     * @return string[]
     */
    protected function getFilter(): array
    {
        return [
            'matches.borrower.age',
            'matches.borrower.maximum_term_allowed',
            'matches.borrower.repricing_frequency',
            'matches.borrower.interest_rate',
            'matches.property.sku',
            'matches.property.name',
            'matches.property.brand',
            'matches.property.category',
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

    /**
     * This function processes the data
     * to return the desired output
     * i.e., just the product sku.
     *
     * @param MatchData $data
     * @param int $limit
     * @param int $verbose
     * @return array
     */
    protected function transform(MatchData $data, int $limit, int $verbose): array
    {
        $i = 0;
        $response = [];
        foreach ($data->matches->toArray() as $record) {
            $response [] = match ($verbose) {
                0 => $record['property']['sku'],
                1 => $record['property'],
                default => $record
            };
            $i++;
            if ($i == $limit)
                break;
        };

        return $response;
    }
}
