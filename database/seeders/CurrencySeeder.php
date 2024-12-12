<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            [
                'name' => 'Peso Colombiano',
                'code' => 'COP',
                'symbol' => '$',
                'decimals' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Dólar Estadounidense',
                'code' => 'USD',
                'symbol' => '$',
                'decimals' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
                'decimals' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Peso Mexicano',
                'code' => 'MXN',
                'symbol' => '$',
                'decimals' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Real Brasileño',
                'code' => 'BRL',
                'symbol' => 'R$',
                'decimals' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Peso Chileno',
                'code' => 'CLP',
                'symbol' => '$',
                'decimals' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Sol Peruano',
                'code' => 'PEN',
                'symbol' => 'S/',
                'decimals' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Peso Argentino',
                'code' => 'ARS',
                'symbol' => '$',
                'decimals' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Boliviano',
                'code' => 'BOB',
                'symbol' => 'Bs.',
                'decimals' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Peso Uruguayo',
                'code' => 'UYU',
                'symbol' => '$',
                'decimals' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($currencies as $currency) {
            DB::table('currencies')->insert(array_merge($currency, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
