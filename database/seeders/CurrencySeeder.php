<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $buffer = fopen(base_path('/database/currency_code.csv'), "r");
        if ($buffer === false) {
            throw new \RuntimeException('Failed to open file for reading');
        }

        $data = [];
        while ($row = fgetcsv($buffer)) {
            $code = $row[2];
            if (empty($code)) {
                continue;
            }

            $data[$code] = [
                'code' => $code,
                'number' => $row[3],
                'name' => $row[1],
            ];
        }

        DB::table('currencies')->insert(array_values($data));
    }
}
