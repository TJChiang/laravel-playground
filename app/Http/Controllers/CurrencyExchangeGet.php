<?php

namespace App\Http\Controllers;

use App\Exceptions\Currency\NotFoundException;
use App\Http\Requests\CurrencyExchangeGetRequest as Request;
use App\Http\Resources\CurrencyExchangeGetResource;
use App\Services\CurrencyExchangeService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CurrencyExchangeGet
{
    /**
     * @throws NotFoundException
     */
    public function __invoke(Request $request, CurrencyExchangeService $currencyExchangeService): CurrencyExchangeGetResource
    {
        $source = $request->input('source');
        $target = $request->input('target');
        $amount = $request->input('amount');

        try {
            $result = $currencyExchangeService->exchangeRate($source, $target, $amount);
        } catch (ModelNotFoundException) {
            throw new NotFoundException('Currency code unrecognized.');
        }

        return new CurrencyExchangeGetResource($result);
    }
}
