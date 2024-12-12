<?php

namespace App\Models\Document\Commercial;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Document\DocLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Order extends Model
{
    protected $fillable = [
        'company_id',
        'branch_id',
        'partner_type',
        'partner_id',
        'type',
        'number',
        'quote_id',
        'date',
        'delivery_date',
        'currency_code',
        'exchange_rate',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total',
        'notes',
        'status',
        'metadata'
    ];

    protected $casts = [
        'date' => 'date',
        'delivery_date' => 'date',
        'exchange_rate' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
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

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function logs()
    {
        return $this->morphMany(DocLog::class, 'documentable');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'confirmed']);
    }

    public function isEditable()
    {
        return in_array($this->status, ['draft']);
    }

    public function calculateTotals()
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->discount_amount = $this->items->sum('discount_amount');
        $this->tax_amount = $this->items->sum('tax_amount');
        $this->total = $this->subtotal - $this->discount_amount + $this->tax_amount;
        $this->save();
    }

    public function updateDeliveryStatus()
    {
        $totalQuantity = $this->items->sum('quantity');
        $deliveredQuantity = $this->items->sum('delivered_quantity');

        if ($deliveredQuantity === 0) {
            $this->status = 'confirmed';
        } elseif ($deliveredQuantity < $totalQuantity) {
            $this->status = 'partial';
        } else {
            $this->status = 'completed';
        }

        $this->save();
    }
}
