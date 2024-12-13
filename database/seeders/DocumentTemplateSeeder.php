<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document\Commercial\DocumentTemplate;

class DocumentTemplateSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            [
                'name' => 'Factura Estándar',
                'description' => 'Plantilla estándar para facturas',
                'document_type' => 'invoice',
                'content' => json_encode([
                    'header' => [
                        'title' => 'FACTURA DE VENTA',
                        'logo' => true,
                        'company_info' => true,
                        'customer_info' => true
                    ],
                    'body' => [
                        'items_table' => [
                            'columns' => ['Código', 'Descripción', 'Cantidad', 'Precio', 'IVA', 'Total']
                        ],
                        'totals' => ['subtotal', 'taxes', 'total']
                    ],
                    'footer' => [
                        'notes' => true,
                        'terms' => true,
                        'signatures' => true
                    ]
                ]),
                'fields' => json_encode([
                    'company_name' => ['type' => 'text', 'required' => true],
                    'company_nit' => ['type' => 'text', 'required' => true],
                    'customer_name' => ['type' => 'text', 'required' => true],
                    'customer_id' => ['type' => 'text', 'required' => true],
                    'date' => ['type' => 'date', 'required' => true],
                    'due_date' => ['type' => 'date', 'required' => true],
                    'items' => ['type' => 'array', 'required' => true],
                    'notes' => ['type' => 'textarea', 'required' => false],
                    'terms' => ['type' => 'textarea', 'required' => false]
                ]),
                'validation_rules' => json_encode([
                    'items' => 'required|array|min:1',
                    'items.*.product_id' => 'required|exists:products,id',
                    'items.*.quantity' => 'required|numeric|min:0.01',
                    'items.*.price' => 'required|numeric|min:0'
                ]),
                'is_default' => true
            ],
            [
                'name' => 'Cotización Detallada',
                'description' => 'Plantilla detallada para cotizaciones',
                'document_type' => 'quote',
                'content' => json_encode([
                    'header' => [
                        'title' => 'COTIZACIÓN',
                        'logo' => true,
                        'company_info' => true,
                        'customer_info' => true,
                        'validity' => true
                    ],
                    'body' => [
                        'items_table' => [
                            'columns' => ['Código', 'Descripción', 'Cantidad', 'Precio', 'Descuento', 'Total']
                        ],
                        'totals' => ['subtotal', 'discount', 'taxes', 'total']
                    ],
                    'footer' => [
                        'notes' => true,
                        'terms' => true,
                        'payment_terms' => true
                    ]
                ]),
                'fields' => json_encode([
                    'company_name' => ['type' => 'text', 'required' => true],
                    'customer_name' => ['type' => 'text', 'required' => true],
                    'validity_days' => ['type' => 'number', 'required' => true],
                    'items' => ['type' => 'array', 'required' => true],
                    'payment_terms' => ['type' => 'textarea', 'required' => true]
                ]),
                'validation_rules' => json_encode([
                    'validity_days' => 'required|integer|min:1',
                    'items' => 'required|array|min:1',
                    'payment_terms' => 'required|string|min:10'
                ]),
                'is_default' => true
            ]
        ];

        foreach ($templates as $template) {
            DocumentTemplate::create($template);
        }
    }
}
