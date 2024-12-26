<?php

use Homeful\Property\Enums\MarketSegment;
use Homeful\Common\Enums\WorkArea;
use Homeful\Borrower\Borrower;
use Homeful\Property\Property;
use Illuminate\Support\Carbon;
use Homeful\Mortgage\Mortgage;

test('that true is true', function () {

    $monthly_gross_income = 60000;
    $date_of_birth = Carbon::parse('1999-03-17');
    $tcp = 2707500.0;

    $borrower = (new Borrower)
        ->setGrossMonthlyIncome($monthly_gross_income)
        ->setBirthdate($date_of_birth);
    expect($term = $borrower->getMaximumTermAllowed())->toBe(30);
    expect($borrower->getRegional())->toBeFalse();
    expect($borrower->getWorkArea())->toBe(WorkArea::HUC);
    expect($borrower->getAffordabilityRates()->getInterestRate())->toBe(0.0625);

    $property = (new Property)
        ->setTotalContractPrice($tcp)
        ->setAppraisedValue($tcp *  0.95);
    expect($property->getMarketSegment())->toBe(MarketSegment::OPEN);
    expect($interest = $property->getDefaultAnnualInterestRateFromBorrower($borrower))->toBe(0.07);
    expect($income_multiplier = $property->getDisposableIncomeRequirementMultiplier())->toBe(0.3);
    expect($property->getLoanableValueMultiplier())->toBe(0.9);
    expect($property->getLoanableValue()->inclusive()->getAmount()->toFloat())->toBe($tcp * 0.9 * 0.95);

    $params = [];

//
//    $tcp = 2850000;
//    $dp = 0.05;
//    $balance = 2850000 * (1 - 0.05);
//    $cash_out = 20000 + 10000;
//    $mf = 0.05 * $tcp;
//    $loan = $balance + $mf;

    $mortgage = new Mortgage($property, $borrower, $params);
//    $balance_payment = $tcp * (1-$params[Input::PERCENT_DP]);
    $percent_dp = $property->getPercentDownPayment();
    $balance_payment = $tcp * (1-$percent_dp);
    expect($mortgage->getBalancePayment()->inclusive()->getAmount()->toFloat())->toBe($balance_payment);
    $percent_mf = $property->getPercentMiscellaneousFees();
    $partial_mf = round($tcp * $percent_mf * $percent_dp, 2);
    expect($mortgage->getPartialMiscellaneousFees()->inclusive()->getAmount()->toFloat())->toBe($partial_mf);
    $balance_mf = round($tcp * $percent_mf * (1-$percent_dp), 2);
    expect($mortgage->getBalanceMiscellaneousFees()->inclusive()->getAmount()->toFloat())->toBe($balance_mf);
    $loan = $balance_payment + $balance_mf;
    expect($mortgage->getLoan()->getPrincipal()->inclusive()->getAmount()->toFloat())->toBe($loan);
    expect($mortgage->getLoan()->getInterestRate())->toBe($interest);
    expect($mortgage->getLoan()->getTerm()->yearsToPay())->toBe($term);

    $disposable_income = $income_multiplier * $monthly_gross_income;
    expect($mortgage->getLoan()->getMonthlyAmortization()->inclusive()->getAmount()->toFloat())->toBeLessThan($disposable_income);
    expect($mortgage->getLoan()->getIncomeRequirement()->getAmount()->toFloat())->toBeLessThan($monthly_gross_income);
    expect($mortgage->getLoanDifference()->inclusive()->getAmount()->toFloat())->toBeLessThan(0.0);
});
