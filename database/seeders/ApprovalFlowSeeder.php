<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document\Commercial\ApprovalFlow;
use Spatie\Permission\Models\Role;

class ApprovalFlowSeeder extends Seeder
{
    public function run()
    {
        // Obtener los IDs de los roles
        $supervisorRole = Role::where('name', 'supervisor')->first();
        $managerRole = Role::where('name', 'manager')->first();
        $financialRole = Role::where('name', 'financial')->first();

        $flows = [
            [
                'name' => 'Aprobación de Facturas',
                'description' => 'Flujo de aprobación para facturas de alto valor',
                'document_type' => 'invoice',
                'conditions' => json_encode([
                    ['field' => 'total', 'operator' => '>=', 'value' => 1000000]
                ]),
                'is_active' => true,
                'steps' => [
                    [
                        'name' => 'Revisión Supervisor',
                        'description' => 'Revisión inicial por supervisor',
                        'type' => 'review',
                        'approver_type' => 'role',
                        'approver_id' => $supervisorRole->id,
                        'order' => 1,
                        'is_required' => true,
                        'timeout_hours' => 24,
                        'on_timeout_action' => 'reject'
                    ],
                    [
                        'name' => 'Aprobación Gerente',
                        'description' => 'Aprobación final por gerente',
                        'type' => 'approval',
                        'approver_type' => 'role',
                        'approver_id' => $managerRole->id,
                        'order' => 2,
                        'is_required' => true,
                        'timeout_hours' => 48,
                        'on_timeout_action' => 'reject'
                    ]
                ]
            ],
            [
                'name' => 'Aprobación de Órdenes',
                'description' => 'Flujo de aprobación para órdenes de compra',
                'document_type' => 'order',
                'conditions' => json_encode([
                    ['field' => 'total', 'operator' => '>=', 'value' => 500000]
                ]),
                'is_active' => true,
                'steps' => [
                    [
                        'name' => 'Revisión Departamento',
                        'description' => 'Revisión por jefe de departamento',
                        'type' => 'review',
                        'approver_type' => 'department',
                        'approver_id' => null,
                        'order' => 1,
                        'is_required' => true,
                        'timeout_hours' => 24,
                        'on_timeout_action' => 'skip'
                    ],
                    [
                        'name' => 'Aprobación Financiera',
                        'description' => 'Aprobación por departamento financiero',
                        'type' => 'approval',
                        'approver_type' => 'role',
                        'approver_id' => $financialRole->id,
                        'order' => 2,
                        'is_required' => true,
                        'timeout_hours' => 48,
                        'on_timeout_action' => 'reject'
                    ]
                ]
            ]
        ];

        foreach ($flows as $flowData) {
            $steps = $flowData['steps'];
            unset($flowData['steps']);
            
            $flow = ApprovalFlow::create($flowData);

            foreach ($steps as $stepData) {
                $flow->steps()->create($stepData);
            }
        }
    }
}
