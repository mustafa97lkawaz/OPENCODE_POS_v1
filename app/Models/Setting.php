<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'printer_type',
        'printer_name',
        'receipt_header',
        'receipt_footer',
        'vat_number',
        'currency_symbol',
        'store_name',
    ];
}