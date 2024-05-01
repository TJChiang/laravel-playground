<?php

namespace App\Http\Requests;

use App\Rules\CurrencyCodeRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class CurrencyExchangeGetRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $amount = $this->input('amount');
        if (is_string($amount)) {
            $this->offsetSet('amount', Str::remove(',', $amount));
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'source' => [
                'required',
                'string',
                'different:target',
                new CurrencyCodeRule(),
            ],
            'target' => [
                'required',
                'string',
                'different:source',
                new CurrencyCodeRule(),
            ],
            'amount' => [
                'required',
                'numeric',
                'regex:/^\d+(\.\d{1,2})?$/',
                'min:0',
            ],
        ];
    }
}
