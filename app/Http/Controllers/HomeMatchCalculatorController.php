<?php

namespace App\Http\Controllers;

use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Money\Exception\MoneyMismatchException;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;
use Homeful\Borrower\Borrower;
use Homeful\Borrower\Exceptions\BirthdateNotSet;
use Homeful\Borrower\Exceptions\MaximumBorrowingAgeBreached;
use Homeful\Borrower\Exceptions\MinimumBorrowingAgeNotMet;
use Homeful\Common\Classes\Input;
use Homeful\Mortgage\Mortgage;
use Homeful\Property\Exceptions\MaximumContractPriceBreached;
use Homeful\Property\Exceptions\MinimumContractPriceBreached;
use Homeful\Property\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelData\Attributes\Validation\In;
use Whitecube\Price\Price;
use Homeful\Mortgage\Data\MortgageData;

class HomeMatchCalculatorController extends Controller
{
    /**
     * Display the registration view.
     */
    public function index(): Response
    {
        return Inertia::render('Calculator');
    }

    /**
     * @throws MaximumContractPriceBreached
     * @throws BirthdateNotSet
     * @throws MinimumBorrowingAgeNotMet
     * @throws MathException
     * @throws NumberFormatException
     * @throws MinimumContractPriceBreached
     * @throws MoneyMismatchException
     * @throws UnknownCurrencyException
     * @throws MaximumBorrowingAgeBreached
     * @throws RoundingNecessaryException
     * @throws ValidationException
     */
    public function calculate(Request $request)
    {
        $validated = validator($request->all(), [
            'age' => ['required', 'numeric'],
            'tcp' => ['required', 'numeric'],
            'gmi' => ['nullable', 'numeric'],
            'percent_dp' => ['required', 'numeric'],
            'dp_term' => ['required', 'integer'],
            'percent_mf' => ['required', 'numeric'],
            'gmi_percent' => ['nullable', 'numeric'],
            'bp_term' => ['nullable', 'integer'],
            'processing_fee' => ['required', 'numeric'],
            'interest_rate' => ['nullable', 'numeric'],
            'mri_fi' => ['required', 'boolean'],
        ])->validate();

        $params = [
            Input::TCP => $tcp = Arr::get($validated , 'tcp'),
            Input::WAGES => $gmi = Arr::get($validated , 'gmi'),
            Input::PERCENT_DP => $percent_dp = Arr::get($validated , 'percent_dp')/100,
            Input::DP_TERM => $dp_term = Arr::get($validated , 'dp_term'),
//            Input::BP_INTEREST_RATE => $interest_rate = Arr::get($validated , 'interest_rate')/100,
            Input::PERCENT_MF => $percent_mf = Arr::get($validated , 'percent_mf')/100,
            Input::BP_TERM => $bp_term = Arr::get($validated , 'bp_term'),
            Input::PROCESSING_FEE => $processing_fee = Arr::get($validated , 'processing_fee'),
        ];

        $property = (new Property)
            ->setTotalContractPrice(new Price(Money::of($tcp, 'PHP')))
            ->setAppraisedValue(new Price(Money::of($tcp, 'PHP')));

        $age = Arr::get($validated, 'age');
        $borrower = (new Borrower($property))
            ->setAge($age)
            ->setRegional(false)
            ->setGrossMonthlyIncome($gmi);

        $params = array_merge($params, [
            Input::BP_TERM => is_null($bp_term)
                ? $borrower->getMaximumTermAllowed()
                : min($bp_term, $borrower->getMaximumTermAllowed()),
        ]);

        $insurances = (bool) Arr::get($validated, 'mri_fi', false)
            ? [
                Input::MORTGAGE_REDEMPTION_INSURANCE => ($tcp / 1000) * 0.225,
                Input::ANNUAL_FIRE_INSURANCE         => $tcp * 0.00212584,
            ]
            : [];

        $params = array_merge($params, $insurances);

        $gmi_percent = Arr::get($validated, 'gmi_percent');
        if ($gmi_percent) {
            $params = array_merge($validated, [
                Input::INCOME_REQUIREMENT_MULTIPLIER => $gmi_percent/100
            ]);
        }
        $interest_rate = Arr::get($validated, 'interest_rate');
        if ($interest_rate) {
            $params = array_merge($validated, [
                Input::BP_INTEREST_RATE => $interest_rate/100
            ]);
        }


        $mortgage = new Mortgage(property: $property, borrower: $borrower, params: $params);

        $data = MortgageData::fromObject($mortgage);

        return back()->with('event', [
            'name' => 'calculated',
            'data' => $data,
        ]);
    }
}
