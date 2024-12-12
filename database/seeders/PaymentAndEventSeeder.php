<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentAndEventSeeder extends Seeder
{
    public function run(): void
    {
        // Medios de pago
        $paymentMeans = [
            ['code' => '1', 'name' => 'Contado', 'group' => 'cash'],
            ['code' => '2', 'name' => 'Crédito', 'group' => 'credit'],
        ];

        foreach ($paymentMeans as $means) {
            DB::table('payment_means')->insert([
                'name' => $means['name'],
                'code' => $means['code'],
                'group' => $means['group'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Métodos de pago
        $paymentMethods = [
            ['code' => '10', 'name' => 'Efectivo'],
            ['code' => '20', 'name' => 'Cheque'],
            ['code' => '41', 'name' => 'Transferencia bancaria'],
            ['code' => '42', 'name' => 'Consignación bancaria'],
            ['code' => '47', 'name' => 'Tarjeta débito'],
            ['code' => '48', 'name' => 'Tarjeta crédito'],
            ['code' => '49', 'name' => 'Transferencia débito bancaria'],
        ];

        foreach ($paymentMethods as $method) {
            DB::table('payment_methods')->insert([
                'name' => $method['name'],
                'code' => $method['code'],
                'payment_means_id' => $method['code'] === '10' ? 1 : 2, // 1: Contado, 2: Crédito
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Tributos
        $taxes = [
            [
                'code' => '01',
                'name' => 'IVA',
                'type' => 'IVA',
                'default_rate' => 19.00,
                'is_retention' => false
            ],
            [
                'code' => '04',
                'name' => 'Impuesto al Consumo',
                'type' => 'INC',
                'default_rate' => 8.00,
                'is_retention' => false
            ],
            [
                'code' => '06',
                'name' => 'ReteFuente por renta',
                'type' => 'RET_FUENTE',
                'default_rate' => 2.50,
                'is_retention' => true
            ],
        ];

        foreach ($taxes as $tax) {
            DB::table('taxes')->insert([
                'name' => $tax['name'],
                'code' => $tax['code'],
                'type' => $tax['type'],
                'default_rate' => $tax['default_rate'],
                'is_retention' => $tax['is_retention'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Tipos de eventos
        $eventTypes = [
            ['code' => '030', 'name' => 'Acuse de recibo', 'group' => 'acknowledgment'],
            ['code' => '031', 'name' => 'Reclamo', 'group' => 'claim'],
            ['code' => '032', 'name' => 'Recibo del bien o servicio', 'group' => 'receipt'],
            ['code' => '033', 'name' => 'Aceptación expresa', 'group' => 'acceptance'],
            ['code' => '034', 'name' => 'Aceptación tácita', 'group' => 'acceptance'],
        ];

        foreach ($eventTypes as $type) {
            DB::table('event_types')->insert([
                'name' => $type['name'],
                'code' => $type['code'],
                'group' => $type['group'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
