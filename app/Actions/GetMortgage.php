<?php

namespace App\Actions;

use Homeful\Borrower\Borrower;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\Mortgage\Data\MortgageData;
use Lorisleiva\Actions\ActionRequest;
use Homeful\Common\Classes\Input;
use Homeful\Mortgage\Mortgage;
use Homeful\Property\Property;
use Illuminate\Support\Arr;

class GetMortgage
{
    use AsAction;

    protected MortgageData $data;

    protected function getMortgage(array $validated)
    {
        $property = (new Property)
            ->setTotalContractPrice($tcp = $validated[Input::TCP])
            ->setAppraisedValue($tcp)
        ;

        $age = Arr::pull($validated, 'age');
        $borrower = (new Borrower)
            ->setGrossMonthlyIncome($validated[Input::WAGES])
            ->setAge($age);

        $mortgage = new Mortgage($property, $borrower, $validated);

        return MortgageData::fromObject($mortgage);
    }

    public function handle(array $params)
    {
        $validated = validator($params, $this->rules())->validate();

        return $this->getMortgage($validated);
    }

    public function asController(ActionRequest $request)
    {
        $this->data = $this->getMortgage($request->validated());
    }

    public function jsonResponse()
    {
        return $this->data;
    }

    public function rules(): array
    {
        return [
            'age' => ['required', 'numeric'],
            Input::WAGES => ['required', 'numeric'],
            Input::TCP => ['required', 'numeric'],
            Input::PERCENT_DP => ['required', 'numeric'],
            Input::DP_TERM => ['required', 'integer'],
            Input::BP_INTEREST_RATE => ['nullable', 'numeric'],
            Input::PERCENT_MF => ['required', 'numeric'],
            Input::BP_TERM => ['required', 'integer'],

            Input::INCOME_REQUIREMENT_MULTIPLIER => ['nullable', 'numeric'],
            Input::PROCESSING_FEE => ['nullable', 'numeric'],
            Input::MORTGAGE_REDEMPTION_INSURANCE => ['nullable', 'numeric'],
            Input::ANNUAL_FIRE_INSURANCE => ['nullable', 'numeric'],
        ];
    }
}
