<?php

namespace App\Models\Document\Commercial;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Customer\Customer;
use App\Models\Document\Commercial\AllowanceCharge;
use App\Models\Document\Commercial\DocumentReference;
use App\Models\Document\Commercial\TaxWithholding;
use App\Models\Document\DianLog;
use App\Models\Document\DocLog;
use App\Models\Document\ErrorLog;
use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    protected $fillable = [
        'company_id',
        'branch_id',
        'customer_id',
        'invoice_id',
        'number',
        'prefix',
        'date',
        'currency_code',
        'exchange_rate',
        'subtotal',
        'tax_amount',
        'total',
        'notes',
        'status',
        'uuid',
        'qr_data',
        'metadata'
    ];

    protected $casts = [
        'date' => 'date',
        'exchange_rate' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'qr_data' => 'json',
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function items()
    {
        return $this->hasMany(CreditNoteItem::class);
    }

    public function logs()
    {
        return $this->morphMany(DocLog::class, 'documentable');
    }

    public function dianLogs()
    {
        return $this->morphMany(DianLog::class, 'documentable');
    }

    public function errorLogs()
    {
        return $this->morphMany(ErrorLog::class, 'documentable');
    }

    public function allowanceCharges()
    {
        return $this->morphMany(AllowanceCharge::class, 'chargeable');
    }

    public function documentReferences()
    {
        return $this->morphMany(DocumentReference::class, 'referenceable');
    }

    public function taxWithholdings()
    {
        return $this->morphMany(TaxWithholding::class, 'withholdable');
    }

    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function isEditable()
    {
        return in_array($this->status, ['draft']);
    }

    public function calculateTotals()
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->tax_amount = $this->items->sum('tax_amount');
        $this->total = $this->subtotal + $this->tax_amount;
        $this->save();
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($creditNote) {
            // Actualizar saldo de la factura
            if ($creditNote->status === 'approved') {
                $creditNote->invoice->registerPayment(
                    $creditNote->total,
                    'credit_note',
                    $creditNote->number,
                    'Nota crédito ' . $creditNote->number
                );
            }
        });
    }
}
