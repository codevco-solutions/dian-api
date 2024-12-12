<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IdentificationTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Registro civil',
                'code' => '11',
                'description' => 'Registro civil de nacimiento',
                'is_active' => true,
            ],
            [
                'name' => 'Tarjeta de identidad',
                'code' => '12',
                'description' => 'Tarjeta de identidad',
                'is_active' => true,
            ],
            [
                'name' => 'Cédula de ciudadanía',
                'code' => '13',
                'description' => 'Documento de identidad nacional',
                'is_active' => true,
            ],
            [
                'name' => 'Tarjeta de extranjería',
                'code' => '21',
                'description' => 'Tarjeta de extranjería',
                'is_active' => true,
            ],
            [
                'name' => 'Cédula de extranjería',
                'code' => '22',
                'description' => 'Cédula de extranjería',
                'is_active' => true,
            ],
            [
                'name' => 'NIT',
                'code' => '31',
                'description' => 'Número de identificación tributaria',
                'is_active' => true,
            ],
            [
                'name' => 'Pasaporte',
                'code' => '41',
                'description' => 'Pasaporte',
                'is_active' => true,
            ],
            [
                'name' => 'PEP',
                'code' => '91',
                'description' => 'Permiso Especial de Permanencia',
                'is_active' => true,
            ],
            [
                'name' => 'PPT',
                'code' => '92',
                'description' => 'Permiso por Protección Temporal',
                'is_active' => true,
            ],
            [
                'name' => 'NUIP',
                'code' => '93',
                'description' => 'Número Único de Identificación Personal',
                'is_active' => true,
            ],
            [
                'name' => 'NES',
                'code' => '94',
                'description' => 'Número de Establecimiento',
                'is_active' => true,
            ],
            [
                'name' => 'Documento extranjero',
                'code' => '42',
                'description' => 'Documento de identificación extranjero',
                'is_active' => true,
            ],
        ];

        foreach ($types as $type) {
            DB::table('identification_types')->insert(array_merge($type, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
