<?php

namespace App\Services;

use App\Repositories\CurrencyRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CurrencyExchangeService
{
    public function __construct(protected CurrencyRepository $currencyRepository)
    {}

    /**
     * @throws ModelNotFoundException
     */
    public function exchangeRate(string $source, string $target, float $amount, int $precise = 2): string
    {
        $entity = $this->currencyRepository->getRatesByCode($source);
        $rate = $entity["{$target}_rate"];
        return number_format($amount * $rate, $precise, '.', ',');
    }
}
