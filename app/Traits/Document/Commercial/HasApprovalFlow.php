<?php

namespace App\Traits\Document\Commercial;

use App\Models\Document\Commercial\ApprovalFlow;
use App\Models\Document\Commercial\ApprovalInstance;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasApprovalFlow
{
    /**
     * Las instancias de aprobación del documento
     */
    public function approvalInstances(): MorphMany
    {
        return $this->morphMany(ApprovalInstance::class, 'document');
    }

    /**
     * Obtener la instancia de aprobación activa
     */
    public function getActiveApprovalInstance(): ?ApprovalInstance
    {
        return $this->approvalInstances()
            ->where('status', 'in_progress')
            ->latest()
            ->first();
    }

    /**
     * Iniciar un flujo de aprobación
     */
    public function startApprovalFlow(?ApprovalFlow $flow = null): ?ApprovalInstance
    {
        // Si ya hay un flujo activo, no iniciar otro
        if ($this->getActiveApprovalInstance()) {
            return null;
        }

        // Si no se especifica un flujo, buscar uno que coincida
        if (!$flow) {
            $flow = ApprovalFlow::where('document_type', $this->getDocumentType())
                ->where('is_active', true)
                ->get()
                ->first(function ($flow) {
                    return $flow->matchesConditions($this);
                });

            if (!$flow) {
                return null;
            }
        }

        // Crear instancia de aprobación
        $instance = $this->approvalInstances()->create([
            'approval_flow_id' => $flow->id,
            'document_type' => $this->getDocumentType(),
            'status' => 'in_progress',
            'current_step' => 1
        ]);

        // Crear pasos de la instancia
        foreach ($flow->steps as $step) {
            $instance->steps()->create([
                'approval_flow_step_id' => $step->id,
                'order' => $step->order,
                'status' => 'pending',
                'timeout_at' => $step->getTimeoutDate()
            ]);
        }

        return $instance;
    }

    /**
     * Aprobar el paso actual
     */
    public function approve(\App\Models\User $user, string $comments = null): bool
    {
        $instance = $this->getActiveApprovalInstance();
        if (!$instance) {
            return false;
        }

        $currentStep = $instance->getCurrentStep();
        if (!$currentStep) {
            return false;
        }

        return $currentStep->approve($user, $comments);
    }

    /**
     * Rechazar el paso actual
     */
    public function reject(\App\Models\User $user, string $comments = null): bool
    {
        $instance = $this->getActiveApprovalInstance();
        if (!$instance) {
            return false;
        }

        $currentStep = $instance->getCurrentStep();
        if (!$currentStep) {
            return false;
        }

        return $currentStep->reject($user, $comments);
    }

    /**
     * Verificar si el documento está aprobado
     */
    public function isApproved(): bool
    {
        return $this->approvalInstances()
            ->where('status', 'completed')
            ->exists();
    }

    /**
     * Verificar si el documento está rechazado
     */
    public function isRejected(): bool
    {
        return $this->approvalInstances()
            ->where('status', 'rejected')
            ->exists();
    }

    /**
     * Verificar si el documento está pendiente de aprobación
     */
    public function isPendingApproval(): bool
    {
        return $this->approvalInstances()
            ->where('status', 'in_progress')
            ->exists();
    }

    /**
     * Obtener el historial de aprobaciones
     */
    public function getApprovalHistory(): array
    {
        return $this->approvalInstances()
            ->with(['steps.approver', 'steps.flowStep'])
            ->get()
            ->map(function ($instance) {
                return [
                    'flow_name' => $instance->approvalFlow->name,
                    'status' => $instance->status,
                    'started_at' => $instance->created_at->format('Y-m-d H:i:s'),
                    'completed_at' => $instance->updated_at->format('Y-m-d H:i:s'),
                    'steps' => $instance->steps->map(function ($step) {
                        return [
                            'name' => $step->flowStep->name,
                            'status' => $step->status,
                            'approver' => $step->approver ? [
                                'name' => $step->approver->name,
                                'email' => $step->approver->email
                            ] : null,
                            'approved_at' => $step->approved_at ? $step->approved_at->format('Y-m-d H:i:s') : null,
                            'comments' => $step->comments
                        ];
                    })
                ];
            })
            ->toArray();
    }
}
