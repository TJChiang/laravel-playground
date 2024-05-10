<?php

namespace App\Services;

use App\Repositories\CurrencyRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

class CurrencyExchangeService
{
    public function __construct(protected CurrencyRepository $currencyRepository)
    {}

    /**
     * @throws InvalidArgumentException
     * @throws ModelNotFoundException
     */
    public function exchangeRate(string $source, string $target, string|float $amount, int $precise = 2): string
    {
        $entity = $this->currencyRepository->getRatesByCode($source);
        if (!array_key_exists("{$target}_rate", $entity->toArray())) {
            throw new InvalidArgumentException();
        }

        $rate = $entity["{$target}_rate"];
        return number_format($amount * $rate, $precise, '.', ',');
    }
}
