namespace App\Models\Document\Commercial;

use App\Models\BaseModel;
use App\Models\Company\Company;
use App\Models\Company\Branch;
use App\Models\Customer\Customer;
use App\Models\Document\Commercial\Order;
use App\Models\Document\Commercial\CreditNote;
use App\Models\Document\Commercial\DebitNote;
use App\Models\Document\Commercial\InvoiceItem;
use App\Models\Document\Commercial\AllowanceCharge;
use App\Models\Document\Commercial\PaymentMeans;
use App\Models\Document\Commercial\DocumentReference;
use App\Models\Document\Commercial\TaxWithholding;
use App\Models\Document\Log;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'branch_id',
        'customer_id',
        'order_id',
        'date',
        'due_date',
        'currency',
        'exchange_rate',
        'payment_method',
        'payment_term',
        'notes',
        'status',
        'approved_at',
        'cancelled_at'
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'exchange_rate' => 'decimal:2',
        'approved_at' => 'datetime',
        'cancelled_at' => 'datetime'
    ];

    protected $appends = [
        'subtotal',
        'tax_amount',
        'total'
    ];

    // Relaciones
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

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function creditNotes()
    {
        return $this->hasMany(CreditNote::class);
    }

    public function debitNotes()
    {
        return $this->hasMany(DebitNote::class);
    }

    public function logs()
    {
        return $this->morphMany(Log::class, 'loggable');
    }

    public function allowanceCharges()
    {
        return $this->morphMany(AllowanceCharge::class, 'chargeable');
    }

    public function paymentMeans()
    {
        return $this->hasMany(PaymentMeans::class);
    }

    public function documentReferences()
    {
        return $this->morphMany(DocumentReference::class, 'referenceable');
    }

    public function taxWithholdings()
    {
        return $this->morphMany(TaxWithholding::class, 'withholdable');
    }

    // Atributos calculados
    public function getSubtotalAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->price * (1 - ($item->discount_rate / 100));
        });
    }

    public function getTaxAmountAttribute()
    {
        return $this->items->sum(function ($item) {
            $subtotal = $item->quantity * $item->price * (1 - ($item->discount_rate / 100));
            return $subtotal * ($item->tax_rate / 100);
        });
    }

    public function getTotalAttribute()
    {
        return $this->subtotal + $this->tax_amount;
    }

    // Métodos de negocio
    public function calculateTotals()
    {
        // Los totales se calculan automáticamente a través de los atributos
        return $this;
    }

    public function isEditable()
    {
        return $this->status === 'draft';
    }

    public function isDeletable()
    {
        return $this->status === 'draft';
    }

    public function isApprovable()
    {
        return $this->status === 'draft' && $this->items->count() > 0;
    }

    public function isCancellable()
    {
        return $this->status === 'approved' && !$this->hasNotes();
    }

    public function hasNotes()
    {
        return $this->creditNotes()->count() > 0 || $this->debitNotes()->count() > 0;
    }

    public function canCreateCreditNote()
    {
        return $this->status === 'approved';
    }

    public function canCreateDebitNote()
    {
        return $this->status === 'approved';
    }

    public function requiresDianValidation()
    {
        return $this->status === 'approved';
    }

    public function isValidatedByDian()
    {
        return $this->status === 'approved';
    }

    // Scopes
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }
}
