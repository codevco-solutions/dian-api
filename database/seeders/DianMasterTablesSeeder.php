<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DianMasterTablesSeeder extends Seeder
{
    public function run(): void
    {
        // Tipos de documentos de identidad
        $identificationTypes = [
            ['code' => '11', 'name' => 'Registro civil de nacimiento'],
            ['code' => '12', 'name' => 'Tarjeta de identidad'],
            ['code' => '13', 'name' => 'Cédula de ciudadanía'],
            ['code' => '21', 'name' => 'Tarjeta de extranjería'],
            ['code' => '22', 'name' => 'Cédula de extranjería'],
            ['code' => '31', 'name' => 'NIT'],
            ['code' => '41', 'name' => 'Pasaporte'],
            ['code' => '42', 'name' => 'Documento de identificación extranjero'],
            ['code' => '50', 'name' => 'NIT de otro país'],
            ['code' => '91', 'name' => 'NUIP'],
        ];

        foreach ($identificationTypes as $type) {
            DB::table('identification_types')->insert([
                'name' => $type['name'],
                'code' => $type['code'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Tipos de organizaciones
        $organizationTypes = [
            ['code' => '1', 'name' => 'Persona Jurídica'],
            ['code' => '2', 'name' => 'Persona Natural'],
        ];

        foreach ($organizationTypes as $type) {
            DB::table('organization_types')->insert([
                'name' => $type['name'],
                'code' => $type['code'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Regímenes tributarios
        $taxRegimes = [
            ['code' => '48', 'name' => 'Impuesto sobre las ventas – IVA'],
            ['code' => '49', 'name' => 'No responsable de IVA'],
            ['code' => '04', 'name' => 'Régimen Simple de Tributación'],
        ];

        foreach ($taxRegimes as $regime) {
            DB::table('tax_regimes')->insert([
                'name' => $regime['name'],
                'code' => $regime['code'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Responsabilidades tributarias
        $taxResponsibilities = [
            ['code' => 'O-13', 'name' => 'Gran contribuyente'],
            ['code' => 'O-15', 'name' => 'Autorretenedor'],
            ['code' => 'O-23', 'name' => 'Agente de retención IVA'],
            ['code' => 'O-47', 'name' => 'Régimen Simple de Tributación'],
        ];

        foreach ($taxResponsibilities as $responsibility) {
            DB::table('tax_responsibilities')->insert([
                'name' => $responsibility['name'],
                'code' => $responsibility['code'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Tipos de documento
        $documentTypes = [
            ['code' => '01', 'name' => 'Factura electrónica de venta', 'prefix' => 'FEVA', 'group' => 'invoices'],
            ['code' => '02', 'name' => 'Factura de venta - exportación', 'prefix' => 'FEXP', 'group' => 'invoices'],
            ['code' => '03', 'name' => 'Factura por contingencia', 'prefix' => 'FCON', 'group' => 'invoices'],
            ['code' => '91', 'name' => 'Nota crédito', 'prefix' => 'NC', 'group' => 'credit_notes'],
            ['code' => '92', 'name' => 'Nota débito', 'prefix' => 'ND', 'group' => 'debit_notes'],
            ['code' => '05', 'name' => 'Documento soporte electrónico', 'prefix' => 'DSE', 'group' => 'support_documents'],
        ];

        foreach ($documentTypes as $type) {
            DB::table('document_types')->insert([
                'name' => $type['name'],
                'code' => $type['code'],
                'prefix' => $type['prefix'],
                'group' => $type['group'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

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
