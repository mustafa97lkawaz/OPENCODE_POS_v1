<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'Supplier_name',
        'company_name',
        'contact_person',
        'phone',
        'email',
        'address',
        'balance',
        'notes',
        'Created_by',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    /**
     * Get the total purchases amount for this supplier.
     */
    public function getTotalPurchasesAttribute()
    {
        try {
            return DB::table('purchases')
                ->where('supplier_id', $this->id)
                ->sum('total_amount');
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get the total payments made to this supplier.
     */
    public function getTotalPaymentsAttribute()
    {
        try {
            return DB::table('supplier_payments')
                ->where('supplier_id', $this->id)
                ->sum('amount');
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Calculate current balance.
     */
    public function getCurrentBalanceAttribute()
    {
        return $this->total_purchases - $this->total_payments;
    }
}