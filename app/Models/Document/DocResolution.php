<?php

namespace App\Models\Document;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Company\Company;
use App\Models\Branch\Branch;

class DocResolution extends Model
{
    use SoftDeletes;

    protected $table = 'dian_resolutions';

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
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
