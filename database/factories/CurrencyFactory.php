<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Currency>
 */
class CurrencyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => $this->faker->currencyCode(),
            'name' => $this->faker->currencyCode(),
            'number' => fake()->numerify('###'),
        ];
    }
}
