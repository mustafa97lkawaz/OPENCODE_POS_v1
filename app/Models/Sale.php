<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'subtotal',
        'tax_amount',
        'discount',
        'total',
        'payment_method',
        'cash_amount',
        'card_amount',
        'paid_amount',
        'change_due',
        'Status',
        'Created_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'cash_amount' => 'decimal:2',
        'card_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_due' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public function saleItems()
    {
        return $this->hasMany('App\Models\SaleItem');
    }

    public function returns()
    {
        return $this->hasMany('App\Models\SaleReturn');
    }
}