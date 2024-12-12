<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OperationTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Factura electrónica de venta',
                'code' => '01',
                'description' => 'Factura de Venta Nacional',
                'prefix' => 'FEVA',
                'group' => 'invoices',
                'is_active' => true,
            ],
            [
                'name' => 'Factura de venta - exportación',
                'code' => '02',
                'description' => 'Factura de Venta de Exportación',
                'prefix' => 'FEXP',
                'group' => 'invoices',
                'is_active' => true,
            ],
            [
                'name' => 'Factura por contingencia',
                'code' => '03',
                'description' => 'Factura por Contingencia DIAN',
                'prefix' => 'FCON',
                'group' => 'invoices',
                'is_active' => true,
            ],
            [
                'name' => 'Documento soporte electrónico',
                'code' => '05',
                'description' => 'Documento Soporte en Adquisiciones',
                'prefix' => 'DSE',
                'group' => 'support_documents',
                'is_active' => true,
            ],
            [
                'name' => 'Documento Soporte de Ajuste',
                'code' => '95',
                'description' => 'Nota de Ajuste al Documento Soporte',
                'prefix' => 'DSA',
                'group' => 'support_documents',
                'is_active' => true,
            ],
            [
                'name' => 'Nota crédito',
                'code' => '91',
                'description' => 'Nota Crédito',
                'prefix' => 'NC',
                'group' => 'credit_notes',
                'is_active' => true,
            ],
            [
                'name' => 'Nota débito',
                'code' => '92',
                'description' => 'Nota Débito',
                'prefix' => 'ND',
                'group' => 'debit_notes',
                'is_active' => true,
            ],
            [
                'name' => 'Nómina Individual',
                'code' => '10',
                'description' => 'Documento Soporte de Pago de Nómina Individual',
                'prefix' => 'NOM',
                'group' => 'payroll',
                'is_active' => true,
            ],
            [
                'name' => 'Nómina Individual de Ajuste',
                'code' => '11',
                'description' => 'Documento Soporte de Ajuste de Nómina',
                'prefix' => 'DNA',
                'group' => 'payroll',
                'is_active' => true,
            ],
        ];

        foreach ($types as $type) {
            DB::table('operation_types')->insert(array_merge($type, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
