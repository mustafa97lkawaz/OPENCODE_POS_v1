<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $fillable = [
        'Product_name',
        'category_id',
        'sku',
        'barcode',
        'photo',
        'description',
        'cost_price',
        'sell_price',
        'tax_rate',
        'reorder_point',
        'wac',
        'stock_qty',
        'expire_date',
        'alert_qty',
        'is_variant',
        'variant_name',
        'unit',
        'variations',
        'max_stock',
        'is_featured',
        'is_active',
        'Status',
        'Created_by',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'wac' => 'decimal:2',
        'variations' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'is_variant' => 'boolean',
        'expire_date' => 'date',
    ];

    /**
     * Get variations attribute
     *
     * @param mixed $value
     * @return array|null
     */
    public function getVariationsAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true);
        }
        return $value;
    }

    /**
     * Set variations attribute
     *
     * @param mixed $value
     * @return void
     */
    public function setVariationsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['variations'] = json_encode($value, JSON_UNESCAPED_UNICODE);
        } else {
            $this->attributes['variations'] = $value;
        }
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function saleItems()
    {
        return $this->hasMany('App\Models\SaleItem');
    }

    public function stockAdjustments()
    {
        return $this->hasMany('App\Models\StockAdjustment');
    }
}
