<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    // Funcionalidad común para todos los modelos
    protected $guarded = ['id'];
    
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->created_by && auth()->check()) {
                $model->created_by = auth()->id();
            }
        });
        
        static::updating(function ($model) {
            if (!$model->updated_by && auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }
}
