<?php

namespace App\Rules;

use App\Repositories\CurrencyRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CurrencyCodeRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }

    public function passes($attribute, mixed $value): bool
    {
        return preg_match('/^[A-Z]{3}$/', $value) === 1
            && in_array($value, $this->getCurrencyCodeList());
    }

    public function message(): string
    {
        return 'Invalid currency code.';
    }

    private function getCurrencyCodeList(): array
    {
        /** @var CurrencyRepository $repo */
        $repo = app()->make(CurrencyRepository::class);
        return $repo->getCodeList();
    }
}
