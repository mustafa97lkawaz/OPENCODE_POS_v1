<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id',
        'variation_value',
        'extra_price',
        'stock_qty',
        'barcode',
    ];

    protected $casts = [
        'extra_price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo('App\Models\Products', 'product_id');
    }
}