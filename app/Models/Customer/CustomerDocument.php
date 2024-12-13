<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'document_type',
        'name',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'expiration_date',
        'status',
        'is_required',
        'metadata',
        'notes'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'expiration_date' => 'date',
        'is_required' => 'boolean',
        'metadata' => 'array'
    ];

    /**
     * Tipos de documentos permitidos
     */
    public static $allowedTypes = [
        'rut',
        'camara_comercio',
        'cedula_representante',
        'estados_financieros',
        'referencias_comerciales',
        'referencias_bancarias',
        'otros'
    ];

    /**
     * Tipos MIME permitidos
     */
    public static $allowedMimeTypes = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/jpg',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Verificar si el documento está vencido
     */
    public function isExpired(): bool
    {
        if (!$this->expiration_date) {
            return false;
        }

        return $this->expiration_date->isPast();
    }

    /**
     * Verificar si el documento está por vencer
     */
    public function isAboutToExpire(int $daysThreshold = 30): bool
    {
        if (!$this->expiration_date) {
            return false;
        }

        return $this->expiration_date->diffInDays(now()) <= $daysThreshold;
    }

    /**
     * Obtener URL firmada del documento
     */
    public function getSignedUrl(int $expirationMinutes = 60): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        return \Storage::temporaryUrl(
            $this->file_path,
            now()->addMinutes($expirationMinutes)
        );
    }
}
