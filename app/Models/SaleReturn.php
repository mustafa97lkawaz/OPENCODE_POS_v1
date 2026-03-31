<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'qty',
        'unit_price',
        'total',
        'reason',
        'Created_by',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the sale that owns the return
     */
    public function sale()
    {
        return $this->belongsTo('App\Models\Sale');
    }

    /**
     * Get the product that was returned
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Products');
    }
}
