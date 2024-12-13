<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document\Commercial\RecurringDocument;
use App\Models\Document\Commercial\Invoice;
use App\Models\Document\Commercial\Order;

class RecurringDocumentSeeder extends Seeder
{
    public function run()
    {
        // Crear documentos base primero
        $invoice = Invoice::create([
            'company_id' => 1,
            'branch_id' => 1,
            'customer_id' => 1,
            'type' => 'standard',
            'number' => 'INV-001',
            'date' => now(),
            'due_date' => now()->addDays(30),
            'currency_code' => 'COP',
            'exchange_rate' => 1.0,
            'payment_method' => 'CASH',
            'payment_means' => 'CASH',
            'payment_term_days' => 30,
            'notes' => 'Documento base para facturación recurrente',
            'status' => 'draft'
        ]);

        $order = Order::create([
            'company_id' => 1,
            'branch_id' => 1,
            'partner_type' => 'App\\Models\\Customer\\Customer',
            'partner_id' => 1,
            'type' => 'sale',
            'number' => 'ORD-001',
            'date' => now(),
            'delivery_date' => now()->addDays(7),
            'currency_code' => 'COP',
            'exchange_rate' => 1.0,
            'notes' => 'Documento base para órdenes recurrentes',
            'status' => 'draft'
        ]);

        // Ahora crear los documentos recurrentes
        $recurringConfigs = [
            [
                'document_type' => 'invoice',
                'document_id' => $invoice->id,
                'frequency_type' => 'monthly',
                'frequency_value' => 1,
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'is_active' => true,
                'generation_day' => 1,
                'generation_conditions' => json_encode([
                    [
                        'type' => 'business_day',
                        'value' => true
                    ],
                    [
                        'type' => 'working_hours',
                        'value' => [
                            'start' => 9,
                            'end' => 17
                        ]
                    ]
                ]),
                'metadata' => json_encode([
                    'description' => 'Facturación mensual de servicios',
                    'auto_send' => true
                ])
            ],
            [
                'document_type' => 'order',
                'document_id' => $order->id,
                'frequency_type' => 'weekly',
                'frequency_value' => 2,
                'start_date' => now(),
                'end_date' => now()->addMonths(6),
                'is_active' => true,
                'generation_day' => null,
                'generation_conditions' => json_encode([
                    [
                        'type' => 'business_day',
                        'value' => true
                    ]
                ]),
                'metadata' => json_encode([
                    'description' => 'Orden de compra quincenal',
                    'auto_approve' => false
                ])
            ]
        ];

        foreach ($recurringConfigs as $config) {
            RecurringDocument::create($config);
        }
    }
}
