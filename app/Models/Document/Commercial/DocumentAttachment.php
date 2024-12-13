<?php

namespace App\Models\Document\Commercial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentAttachment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'document_type',
        'document_id',
        'name',
        'description',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by',
        'metadata'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'metadata' => 'array'
    ];

    /**
     * Obtener el documento relacionado basado en el tipo
     */
    public function document()
    {
        switch ($this->document_type) {
            case 'invoice':
                return $this->belongsTo(Invoice::class, 'document_id');
            case 'credit_note':
                return $this->belongsTo(CreditNote::class, 'document_id');
            case 'debit_note':
                return $this->belongsTo(DebitNote::class, 'document_id');
            case 'order':
                return $this->belongsTo(Order::class, 'document_id');
            case 'quote':
                return $this->belongsTo(Quote::class, 'document_id');
            default:
                return null;
        }
    }

    public function uploader()
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }

    /**
     * Obtener URL del archivo
     */
    public function getUrl(): string
    {
        return \Storage::url($this->file_path);
    }

    /**
     * Obtener tamaño del archivo formateado
     */
    public function getFormattedSize(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Verificar si el archivo es una imagen
     */
    public function isImage(): bool
    {
        return in_array($this->file_type, [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ]);
    }

    /**
     * Verificar si el archivo es un PDF
     */
    public function isPdf(): bool
    {
        return $this->file_type === 'application/pdf';
    }
}
