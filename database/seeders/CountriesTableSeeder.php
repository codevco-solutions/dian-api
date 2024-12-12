<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesTableSeeder extends Seeder
{
    public function run(): void
    {
        // Lista de países
        $countries = [
            [
                'name' => 'Colombia',
                'code_2' => 'CO',
                'code_3' => 'COL',
                'numeric_code' => '170',
            ],
            [
                'name' => 'Estados Unidos',
                'code_2' => 'US',
                'code_3' => 'USA',
                'numeric_code' => '840',
            ],
            [
                'name' => 'España',
                'code_2' => 'ES',
                'code_3' => 'ESP',
                'numeric_code' => '724',
            ],
            [
                'name' => 'Perú',
                'code_2' => 'PE',
                'code_3' => 'PER',
                'numeric_code' => '604',
            ],
            [
                'name' => 'Chile',
                'code_2' => 'CL',
                'code_3' => 'CHL',
                'numeric_code' => '152',
            ],
            [
                'name' => 'Brasil',
                'code_2' => 'BR',
                'code_3' => 'BRA',
                'numeric_code' => '076',
            ],
            [
                'name' => 'México',
                'code_2' => 'MX',
                'code_3' => 'MEX',
                'numeric_code' => '484',
            ],
            [
                'name' => 'Argentina',
                'code_2' => 'AR',
                'code_3' => 'ARG',
                'numeric_code' => '032',
            ],
            [
                'name' => 'Ecuador',
                'code_2' => 'EC',
                'code_3' => 'ECU',
                'numeric_code' => '218',
            ],
            [
                'name' => 'Panamá',
                'code_2' => 'PA',
                'code_3' => 'PAN',
                'numeric_code' => '591',
            ],
        ];

        // Insertar países
        foreach ($countries as $country) {
            $countryId = DB::table('countries')->insertGetId([
                'name' => $country['name'],
                'code_2' => $country['code_2'],
                'code_3' => $country['code_3'],
                'numeric_code' => $country['numeric_code'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Solo agregar departamentos para Colombia
            if ($country['code_2'] === 'CO') {
                // Departamentos de Colombia
                $departments = [
                    ['name' => 'Antioquia', 'code' => '05'],
                    ['name' => 'Atlántico', 'code' => '08'],
                    ['name' => 'Bogotá D.C.', 'code' => '11'],
                    ['name' => 'Bolívar', 'code' => '13'],
                    ['name' => 'Boyacá', 'code' => '15'],
                    ['name' => 'Caldas', 'code' => '17'],
                    ['name' => 'Caquetá', 'code' => '18'],
                    ['name' => 'Cauca', 'code' => '19'],
                    ['name' => 'Cesar', 'code' => '20'],
                    ['name' => 'Córdoba', 'code' => '23'],
                    ['name' => 'Cundinamarca', 'code' => '25'],
                    ['name' => 'Chocó', 'code' => '27'],
                    ['name' => 'Huila', 'code' => '41'],
                    ['name' => 'La Guajira', 'code' => '44'],
                    ['name' => 'Magdalena', 'code' => '47'],
                    ['name' => 'Meta', 'code' => '50'],
                    ['name' => 'Nariño', 'code' => '52'],
                    ['name' => 'Norte de Santander', 'code' => '54'],
                    ['name' => 'Quindío', 'code' => '63'],
                    ['name' => 'Risaralda', 'code' => '66'],
                    ['name' => 'Santander', 'code' => '68'],
                    ['name' => 'Sucre', 'code' => '70'],
                    ['name' => 'Tolima', 'code' => '73'],
                    ['name' => 'Valle del Cauca', 'code' => '76'],
                    ['name' => 'Arauca', 'code' => '81'],
                    ['name' => 'Casanare', 'code' => '85'],
                    ['name' => 'Putumayo', 'code' => '86'],
                    ['name' => 'San Andrés y Providencia', 'code' => '88'],
                    ['name' => 'Amazonas', 'code' => '91'],
                    ['name' => 'Guainía', 'code' => '94'],
                    ['name' => 'Guaviare', 'code' => '95'],
                    ['name' => 'Vaupés', 'code' => '97'],
                    ['name' => 'Vichada', 'code' => '99'],
                ];

                foreach ($departments as $department) {
                    DB::table('states')->insert([
                        'country_id' => $countryId,
                        'name' => $department['name'],
                        'code' => $department['code'],
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
