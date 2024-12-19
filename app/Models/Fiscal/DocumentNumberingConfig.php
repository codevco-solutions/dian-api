<?php

namespace App\Models\Fiscal;

use App\Models\BaseModel;
use App\Models\Company\Company;
use App\Models\Company\Branch;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentNumberingConfig extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'branch_id',
        'document_type',
        'prefix',
        'padding',
        'last_number',
        'format',
        'reset_yearly',
        'is_active'
    ];

    protected $casts = [
        'padding' => 'integer',
        'last_number' => 'integer',
        'reset_yearly' => 'boolean',
        'is_active' => 'boolean'
    ];

    // Relaciones
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Métodos
    public function generateNextNumber()
    {
        $nextNumber = $this->last_number + 1;

        if ($this->reset_yearly && $this->shouldResetNumber()) {
            $nextNumber = 1;
        }

        $number = str_pad($nextNumber, $this->padding, '0', STR_PAD_LEFT);
        
        if ($this->format) {
            $number = $this->applyFormat($number);
        } else if ($this->prefix) {
            $number = $this->prefix . $number;
        }

        $this->last_number = $nextNumber;
        $this->save();

        return $number;
    }

    protected function shouldResetNumber()
    {
        if (!$this->updated_at) {
            return false;
        }

        return $this->updated_at->year < now()->year;
    }

    protected function applyFormat($number)
    {
        $replacements = [
            '{PREFIX}' => $this->prefix,
            '{YEAR}' => now()->format('Y'),
            '{MONTH}' => now()->format('m'),
            '{NUMBER}' => $number
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $this->format
        );
    }
}
