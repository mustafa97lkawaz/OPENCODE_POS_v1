<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuspendedSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'items_json',
        'total',
        'Created_by',
    ];

    protected $casts = [
        'items_json' => 'array',
        'total' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }
}