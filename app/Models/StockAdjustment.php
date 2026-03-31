<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'qty_change',
        'type',
        'reason',
        'Created_by',
    ];

    public function product()
    {
        return $this->belongsTo('App\Models\Products');
    }
}