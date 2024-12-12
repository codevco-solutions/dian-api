<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxRegimeSeeder extends Seeder
{
    public function run(): void
    {
        $regimes = [
            [
                'name' => 'Régimen Simple de Tributación',
                'code' => '04',
                'description' => 'RST - SIMPLE',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Régimen Ordinario',
                'code' => '05',
                'description' => 'Régimen Ordinario',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Régimen No Responsable de IVA',
                'code' => '49',
                'description' => 'No responsable de IVA',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tax_regimes')->insert($regimes);
    }
}
