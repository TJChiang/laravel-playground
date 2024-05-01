<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyRateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'currency_code' => $this->faker->currencyCode(),
            'TWD_rate' => $this->faker->randomFloat(2, 0.0001, 150),
            'JPY_rate' => $this->faker->randomFloat(2, 0.0001, 150),
            'USD_rate' => $this->faker->randomFloat(2, 0.0001, 150),
        ];
    }
}
