<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'address',
        'phone',
        'email',
        'is_main',
        'is_active',
        'settings'
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'is_active' => 'boolean',
        'settings' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($branch) {
            $branch->is_active = false;
            $branch->save();
        });

        static::restoring(function ($branch) {
            $branch->is_active = true;
            $branch->save();
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
