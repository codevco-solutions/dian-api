<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesTableSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener los IDs de los departamentos
        $states = DB::table('states')->get()->keyBy('code');

        $cities = [
            // Antioquia
            ['state_code' => '05', 'name' => 'Medellín', 'code' => '001'],
            ['state_code' => '05', 'name' => 'Bello', 'code' => '088'],
            ['state_code' => '05', 'name' => 'Envigado', 'code' => '266'],
            ['state_code' => '05', 'name' => 'Itagüí', 'code' => '360'],
            
            // Atlántico
            ['state_code' => '08', 'name' => 'Barranquilla', 'code' => '001'],
            ['state_code' => '08', 'name' => 'Soledad', 'code' => '758'],
            
            // Bogotá D.C.
            ['state_code' => '11', 'name' => 'Bogotá D.C.', 'code' => '001'],
            
            // Bolívar
            ['state_code' => '13', 'name' => 'Cartagena', 'code' => '001'],
            
            // Boyacá
            ['state_code' => '15', 'name' => 'Tunja', 'code' => '001'],
            ['state_code' => '15', 'name' => 'Duitama', 'code' => '238'],
            ['state_code' => '15', 'name' => 'Sogamoso', 'code' => '759'],
            
            // Caldas
            ['state_code' => '17', 'name' => 'Manizales', 'code' => '001'],
            
            // Caquetá
            ['state_code' => '18', 'name' => 'Florencia', 'code' => '001'],
            
            // Cauca
            ['state_code' => '19', 'name' => 'Popayán', 'code' => '001'],
            
            // Cesar
            ['state_code' => '20', 'name' => 'Valledupar', 'code' => '001'],
            
            // Córdoba
            ['state_code' => '23', 'name' => 'Montería', 'code' => '001'],
            
            // Cundinamarca
            ['state_code' => '25', 'name' => 'Soacha', 'code' => '754'],
            ['state_code' => '25', 'name' => 'Facatativá', 'code' => '269'],
            ['state_code' => '25', 'name' => 'Zipaquirá', 'code' => '899'],
            
            // Huila
            ['state_code' => '41', 'name' => 'Neiva', 'code' => '001'],
            
            // La Guajira
            ['state_code' => '44', 'name' => 'Riohacha', 'code' => '001'],
            
            // Magdalena
            ['state_code' => '47', 'name' => 'Santa Marta', 'code' => '001'],
            
            // Meta
            ['state_code' => '50', 'name' => 'Villavicencio', 'code' => '001'],
            
            // Nariño
            ['state_code' => '52', 'name' => 'Pasto', 'code' => '001'],
            
            // Norte de Santander
            ['state_code' => '54', 'name' => 'Cúcuta', 'code' => '001'],
            
            // Quindío
            ['state_code' => '63', 'name' => 'Armenia', 'code' => '001'],
            
            // Risaralda
            ['state_code' => '66', 'name' => 'Pereira', 'code' => '001'],
            ['state_code' => '66', 'name' => 'Dosquebradas', 'code' => '170'],
            
            // Santander
            ['state_code' => '68', 'name' => 'Bucaramanga', 'code' => '001'],
            ['state_code' => '68', 'name' => 'Floridablanca', 'code' => '276'],
            
            // Sucre
            ['state_code' => '70', 'name' => 'Sincelejo', 'code' => '001'],
            
            // Tolima
            ['state_code' => '73', 'name' => 'Ibagué', 'code' => '001'],
            
            // Valle del Cauca
            ['state_code' => '76', 'name' => 'Cali', 'code' => '001'],
            ['state_code' => '76', 'name' => 'Buenaventura', 'code' => '109'],
            ['state_code' => '76', 'name' => 'Palmira', 'code' => '520'],
            ['state_code' => '76', 'name' => 'Tuluá', 'code' => '834'],
        ];

        foreach ($cities as $city) {
            if (isset($states[$city['state_code']])) {
                DB::table('cities')->insert([
                    'state_id' => $states[$city['state_code']]->id,
                    'name' => $city['name'],
                    'code' => $city['code'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
