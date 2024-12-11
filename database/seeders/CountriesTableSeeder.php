<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesTableSeeder extends Seeder
{
    public function run(): void
    {
        // Insertar Colombia
        $colombiaId = DB::table('countries')->insertGetId([
            'name' => 'Colombia',
            'code_2' => 'CO',
            'code_3' => 'COL',
            'numeric_code' => '170',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Departamentos de Colombia (muestra parcial)
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
                'country_id' => $colombiaId,
                'name' => $department['name'],
                'code' => $department['code'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Nota: Las ciudades se agregarán en un seeder separado debido a la cantidad de datos
    }
}
