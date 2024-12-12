<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Persona Natural',
                'code' => '1',
                'description' => 'Persona Natural',
                'is_active' => true,
            ],
            [
                'name' => 'Persona Jurídica',
                'code' => '2',
                'description' => 'Persona Jurídica',
                'is_active' => true,
            ],
            [
                'name' => 'Sociedad Anónima',
                'code' => 'SA',
                'description' => 'Sociedad Anónima (S.A.)',
                'is_active' => true,
            ],
            [
                'name' => 'Sociedad por Acciones Simplificada',
                'code' => 'SAS',
                'description' => 'Sociedad por Acciones Simplificada (S.A.S.)',
                'is_active' => true,
            ],
            [
                'name' => 'Sociedad Limitada',
                'code' => 'LTDA',
                'description' => 'Sociedad de Responsabilidad Limitada (LTDA)',
                'is_active' => true,
            ],
            [
                'name' => 'Empresa Unipersonal',
                'code' => 'EU',
                'description' => 'Empresa Unipersonal (E.U.)',
                'is_active' => true,
            ],
            [
                'name' => 'Sociedad Colectiva',
                'code' => 'SC',
                'description' => 'Sociedad Colectiva (S.C.)',
                'is_active' => true,
            ],
            [
                'name' => 'Sociedad Comandita Simple',
                'code' => 'SCS',
                'description' => 'Sociedad en Comandita Simple (S. en C.)',
                'is_active' => true,
            ],
            [
                'name' => 'Sociedad Comandita por Acciones',
                'code' => 'SCA',
                'description' => 'Sociedad en Comandita por Acciones (S.C.A.)',
                'is_active' => true,
            ],
            [
                'name' => 'Entidad Sin Ánimo de Lucro',
                'code' => 'ESAL',
                'description' => 'Entidad Sin Ánimo de Lucro (ESAL)',
                'is_active' => true,
            ],
            [
                'name' => 'Sociedad Extranjera',
                'code' => 'SE',
                'description' => 'Sociedad Extranjera',
                'is_active' => true,
            ],
            [
                'name' => 'Consorcio',
                'code' => 'CONS',
                'description' => 'Consorcio',
                'is_active' => true,
            ],
            [
                'name' => 'Unión Temporal',
                'code' => 'UT',
                'description' => 'Unión Temporal',
                'is_active' => true,
            ],
        ];

        foreach ($types as $type) {
            DB::table('organization_types')->insert(array_merge($type, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
