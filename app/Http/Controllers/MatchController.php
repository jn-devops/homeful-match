<?php

namespace App\Http\Controllers;

use App\Actions\MatchProperties;
use App\Http\Requests\MatchRequest;
use Brick\Math\RoundingMode;
use Homeful\Borrower\Borrower;
use Homeful\Common\Classes\Input;
use Homeful\Mortgage\Data\MortgageData;
use Homeful\Property\Property;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Whitecube\Price\Price;
use Homeful\Mortgage\Mortgage;
use Homeful\Payment\Payment;
use Homeful\Payment\Class\Term;

class MatchController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(MatchRequest $request)
    {
        $data = MatchProperties::run($request->all());

        return back()->with('event', [
            'name' => 'match',
            'data' => $data,
        ]);
    }

    public function scratch()
    {
//                dd($request->all());
//
//        $params = [
//            Input::WAGES => 100000,
//            Input::TCP => 2500000,
//            Input::PERCENT_DP => 5 / 100,
//            Input::DP_TERM => 12,
//            Input::BP_INTEREST_RATE => 7 / 100,
//            Input::PERCENT_MF => 8.5 / 100,
//            Input::LOW_CASH_OUT => 0.0,
//            Input::BP_TERM => 20,
//        ];
//
//        $property = (new Property)
//            ->setTotalContractPrice(Price::of($params[Input::TCP], 'PHP'))->setDisposableIncomeRequirementMultiplier(0.32);
//
////        dd($property->getMarketSegment());
////        dd($property->getDevelopmentType());
////        dd($property->getDisposableIncomeRequirementMultiplier());
//
//        $borrower = new Borrower($property);
//        $borrower->setBirthdate(Carbon::parse($request->date_of_birth))
//            ->setGrossMonthlyIncome($params[Input::WAGES])
//            ->setRegional($request->region != 'NCR');
//
////        dd($borrower->getMonthlyDisposableIncome($property)->inclusive()->getAmount()->toFloat());
////        dd($borrower->getAffordabilityRates());
////        dd($borrower->getWorkArea());
////        dd($borrower->getEmploymentType());
//
//
//        $mortgage = Mortgage::createWithTypicalBorrower($property, $params);
////        dd($mortgage->getLoan()->getIncomeRequirement()->getAmount()->toFloat());
//
//        $data = MortgageData::fromObject($mortgage);
//
////        $data = [
////            'abc' => 'def',
////            'ghi' => 'jkl'
////        ];
    }
}
