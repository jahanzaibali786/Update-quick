<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    protected $fillable = [
        'credit_note_id',
        'customer',
        'customer_id',
        'customer_email',
        'date',
        'issue_date',
        'amount',
        'description',
        'category_id',
        'location_of_sale',
        'bill_to',
        'status',
        'subtotal',
        'taxable_subtotal',
        'discount_type',
        'discount_value',
        'total_discount',
        'sales_tax_rate',
        'total_tax',
        'sales_tax_amount',
        'total_amount',
        'attachments',
        'memo',
        'note',
        'created_by',
        'owned_by',
        'payment_id',
        'voucher_id',
    ];

    public function customer_detail()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }
    public function invoice_detail()
    {
        return $this->belongsTo('App\Models\Invoice', 'invoice');
    }

    public function items()
    {
        return $this->hasMany('App\Models\CreditNoteProduct', 'credit_note_id', 'id');
    }

    /**
     * Relationship to invoice (alias for invoice_detail for consistency)
     */
    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice', 'invoice');
    }

    public function getCustomerNameAttribute()
    {
        return $this->customer_detail->name ?? '';
    }
}
