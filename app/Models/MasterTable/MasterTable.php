<?php

namespace App\Models\MasterTable;

use Illuminate\Database\Eloquent\Model;

class MasterTable extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}
