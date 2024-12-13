<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document\Commercial\DocumentState;

class DocumentStateSeeder extends Seeder
{
    public function run()
    {
        $states = [
            [
                'name' => 'Borrador',
                'description' => 'Documento en estado inicial',
                'type' => 'draft',
                'color' => '#808080',
                'icon' => 'draft',
                'order' => 1,
                'next_states' => ['pending', 'cancelled'],
                'metadata' => [
                    'can_edit' => true,
                    'can_delete' => true
                ]
            ],
            [
                'name' => 'Pendiente',
                'description' => 'Documento pendiente de aprobación',
                'type' => 'pending',
                'color' => '#FFA500',
                'icon' => 'pending',
                'order' => 2,
                'next_states' => ['approved', 'rejected', 'cancelled'],
                'metadata' => [
                    'can_edit' => false,
                    'requires_approval' => true
                ]
            ],
            [
                'name' => 'Aprobado',
                'description' => 'Documento aprobado',
                'type' => 'approved',
                'color' => '#008000',
                'icon' => 'approved',
                'order' => 3,
                'next_states' => ['voided'],
                'metadata' => [
                    'can_edit' => false,
                    'is_final' => true
                ]
            ],
            [
                'name' => 'Rechazado',
                'description' => 'Documento rechazado',
                'type' => 'rejected',
                'color' => '#FF0000',
                'icon' => 'rejected',
                'order' => 4,
                'next_states' => ['draft'],
                'metadata' => [
                    'can_edit' => false,
                    'requires_comments' => true
                ]
            ],
            [
                'name' => 'Anulado',
                'description' => 'Documento anulado',
                'type' => 'voided',
                'color' => '#000000',
                'icon' => 'voided',
                'order' => 5,
                'next_states' => [],
                'metadata' => [
                    'can_edit' => false,
                    'is_final' => true,
                    'requires_comments' => true
                ]
            ],
            [
                'name' => 'Cancelado',
                'description' => 'Documento cancelado',
                'type' => 'cancelled',
                'color' => '#800000',
                'icon' => 'cancelled',
                'order' => 6,
                'next_states' => ['draft'],
                'metadata' => [
                    'can_edit' => false,
                    'requires_comments' => true
                ]
            ]
        ];

        foreach ($states as $state) {
            DocumentState::create($state);
        }
    }
}
