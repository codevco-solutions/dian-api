<?php

namespace App\Models\Document\Commercial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DocumentReference extends Model
{
    protected $table = 'document_references';

    protected $fillable = [
        'referenceable_type',
        'referenceable_id',
        'document_type_code',
        'document_type',
        'document_id',
        'issue_date',
        'uuid',
        'uuid_scheme_id',
        'uuid_scheme_name',
    ];

    protected $casts = [
        'issue_date' => 'datetime',
    ];

    /**
     * Get the parent referenceable model (Invoice, CreditNote, DebitNote).
     */
    public function referenceable(): MorphTo
    {
        return $this->morphTo();
    }
}
