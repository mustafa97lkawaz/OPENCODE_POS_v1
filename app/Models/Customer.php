<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'Customer_name',
        'phone',
        'email',
        'address',
        'type',
        'account_balance',
        'Status',
        'Created_by',
    ];

    protected $casts = [
        'account_balance' => 'decimal:2',
    ];

    public function sales()
    {
        return $this->hasMany('App\Models\Sale');
    }

    public function suspendedSales()
    {
        return $this->hasMany('App\Models\SuspendedSale');
    }
}