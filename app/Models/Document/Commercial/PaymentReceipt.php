<?php

namespace App\Models\Document\Commercial;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Document\DocLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PaymentReceipt extends Model
{
    protected $fillable = [
        'company_id',
        'branch_id',
        'partner_type',
        'partner_id',
        'number',
        'date',
        'currency_code',
        'exchange_rate',
        'amount',
        'payment_method',
        'reference',
        'notes',
        'status',
        'metadata'
    ];

    protected $casts = [
        'date' => 'date',
        'exchange_rate' => 'decimal:2',
        'amount' => 'decimal:2',
        'metadata' => 'json'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function partner(): MorphTo
    {
        return $this->morphTo();
    }

    public function details()
    {
        return $this->hasMany(PaymentReceiptDetail::class);
    }

    public function logs()
    {
        return $this->morphMany(DocLog::class, 'documentable');
    }

    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($receipt) {
            if ($receipt->status === 'confirmed') {
                // Registrar los pagos en cada documento
                foreach ($receipt->details as $detail) {
                    $document = $detail->document;
                    if ($document instanceof Invoice) {
                        $document->registerPayment(
                            $detail->amount,
                            $receipt->payment_method,
                            $receipt->reference,
                            'Recibo de pago ' . $receipt->number
                        );
                    }
                }
            }
        });
    }
}
