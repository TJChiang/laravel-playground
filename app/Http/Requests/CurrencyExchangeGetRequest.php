<?php

namespace App\Http\Requests;

use App\Rules\CurrencyCodeRule;
use Illuminate\Support\Str;

class CurrencyExchangeGetRequest extends BaseRequest
{
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
                'regex:/^(?!0\d)(?:\d{1,3}(?:,\d{3})+|\d+)?(?:\.\d{1,2})?$/',
            ],
        ];
    }

    protected function passedValidation(): void
    {
        $amount = $this->input('amount');
        if (is_string($amount)) {
            $this->offsetSet('amount', Str::remove(',', $amount));
        }
    }
}
