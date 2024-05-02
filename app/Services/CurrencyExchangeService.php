<?php

namespace App\Services;

use App\Repositories\CurrencyRepository;
use InvalidArgumentException;

class CurrencyExchangeService
{
    public function __construct(protected CurrencyRepository $currencyRepository)
    {}

    /**
     * @throws InvalidArgumentException
     */
    public function exchangeRate(string $source, string $target, string|float $amount, int $precise = 2): string
    {
        $this->validateCurrencyCode($source);
        $this->validateCurrencyCode($target);
        $this->validateAmount($amount);

        $entity = $this->currencyRepository->getRatesByCode($source);
        $rate = $entity["{$target}_rate"];
        return number_format($amount * $rate, $precise, '.', ',');
    }

    /**
     * @throws InvalidArgumentException
     */
    private function validateCurrencyCode(string $currencyCode): void
    {
        $codeList = $this->currencyRepository->getCodeList();
        if (preg_match('/^[A-Z]{3}$/', $currencyCode) === 1 && in_array($currencyCode, $codeList)) {
            return;
        }

        throw new InvalidArgumentException('Invalid currency code.');
    }

    /**
     * @throws InvalidArgumentException
     */
    private function validateAmount(mixed $amount): void
    {
        if (preg_match('/^\d+(\.\d{1,2})?$/', $amount) === 1) {
            return;
        }

        throw new InvalidArgumentException('Invalid currency amount.');
    }
}
