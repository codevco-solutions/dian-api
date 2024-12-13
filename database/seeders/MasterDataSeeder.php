<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    public function run()
    {
        // Países
        DB::table('countries')->insert([
            [
                'name' => 'Colombia',
                'code_2' => 'CO',
                'code_3' => 'COL',
                'numeric_code' => '170',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Departamentos
        DB::table('states')->insert([
            [
                'country_id' => 1,
                'name' => 'Bogotá D.C.',
                'code' => '11',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Ciudades
        DB::table('cities')->insert([
            [
                'state_id' => 1,
                'name' => 'Bogotá D.C.',
                'code' => '11001',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Tipos de identificación
        DB::table('identification_types')->insert([
            [
                'code' => 'NIT',
                'name' => 'NIT',
                'description' => 'Número de Identificación Tributaria',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'CC',
                'name' => 'Cédula de Ciudadanía',
                'description' => 'Documento de identidad colombiano',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Regímenes tributarios
        DB::table('tax_regimes')->insert([
            [
                'code' => 'RS',
                'name' => 'Régimen Simple',
                'description' => 'Régimen Simple de Tributación',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'RC',
                'name' => 'Régimen Común',
                'description' => 'Régimen Común',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Tipos de organización
        DB::table('organization_types')->insert([
            [
                'code' => 'SAS',
                'name' => 'Sociedad por Acciones Simplificada',
                'description' => 'SAS',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'LTDA',
                'name' => 'Sociedad Limitada',
                'description' => 'LTDA',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Tipos de moneda
        DB::table('currencies')->insert([
            [
                'code' => 'COP',
                'name' => 'Peso Colombiano',
                'symbol' => '$',
                'decimals' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'USD',
                'name' => 'Dólar Estadounidense',
                'symbol' => 'US$',
                'decimals' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Unidades de medida
        DB::table('measurement_units')->insert([
            [
                'code' => 'UND',
                'name' => 'Unidad',
                'description' => 'Unidad básica de medida',
                'group' => 'unit',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'KG',
                'name' => 'Kilogramo',
                'description' => 'Unidad de peso',
                'group' => 'weight',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
