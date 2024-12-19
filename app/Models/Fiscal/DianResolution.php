<?php

namespace App\Models\Fiscal;

use App\Models\BaseModel;
use App\Models\Company\Company;
use App\Models\Company\Branch;
use Illuminate\Database\Eloquent\SoftDeletes;

class DianResolution extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'branch_id',
        'type',
        'resolution_number',
        'prefix',
        'start_date',
        'end_date',
        'start_number',
        'end_number',
        'current_number',
        'is_active',
        'technical_key'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_number' => 'integer',
        'end_number' => 'integer',
        'current_number' => 'integer',
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
    public function getNextNumber()
    {
        if ($this->current_number >= $this->end_number) {
            throw new \Exception('Se ha alcanzado el número máximo permitido por la resolución.');
        }
        return $this->current_number + 1;
    }

    public function incrementNumber()
    {
        $this->current_number = $this->getNextNumber();
        $this->save();
        return $this->current_number;
    }

    public function isValid()
    {
        return $this->is_active &&
            now()->between($this->start_date, $this->end_date) &&
            $this->current_number < $this->end_number;
    }

    public function getRemainingDays()
    {
        return now()->diffInDays($this->end_date, false);
    }

    public function getRemainingNumbers()
    {
        return $this->end_number - $this->current_number;
    }
}
