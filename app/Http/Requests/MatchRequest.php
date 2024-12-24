<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;

/**
 * Class MatchRequest
 *
 * @property float $monthly_gross_income
 * @property Carbon $date_of_birth
 * @property string $region
 */
class MatchRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date_of_birth' => ['required', 'date'],
            'monthly_gross_income' => ['required', 'numeric', 'min:0'],
            'region' => ['required', 'string', 'max:100'],
        ];
    }
}
