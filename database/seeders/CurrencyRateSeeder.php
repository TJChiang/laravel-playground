<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencyRateSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'currency_code' => 'TWD',
                'TWD_rate' => 1,
                'JPY_rate' => 3.669,
                'USD_rate' => 0.03281,
            ],[
                'currency_code' => 'JPY',
                'TWD_rate' => 0.26956,
                'JPY_rate' => 1,
                'USD_rate' => 0.00885,
            ],[
                'currency_code' => 'USD',
                'TWD_rate' => 30.444,
                'JPY_rate' => 111.801,
                'USD_rate' => 1,
            ],
        ];
        DB::connection('mysql')->table('currency_rates')->insert($data);
        DB::connection('mysql_test')->table('currency_rates')->insert($data);
    }
}
