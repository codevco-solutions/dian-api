<?php

namespace App\Models\Document\Commercial;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Customer\Customer;
use App\Models\Document\DocLog;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = [
        'company_id',
        'branch_id',
        'customer_id',
        'number',
        'date',
        'expiration_date',
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
        'expiration_date' => 'date',
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function logs()
    {
        return $this->morphMany(DocLog::class, 'documentable');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['draft', 'sent']);
    }

    public function scopeExpired($query)
    {
        return $query->where('expiration_date', '<', now())
            ->where('status', 'sent');
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
}
