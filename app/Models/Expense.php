<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'Expense_name',
        'reference_number',
        'amount',
        'category_id',
        'expense_date',
        'description',
        'payment_method',
        'attachment',
        'recurring',
        'recurring_type',
        'status',
        'Created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'recurring' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo('App\Models\ExpenseCategory');
    }

    public function getPaymentMethodLabelAttribute()
    {
        $labels = [
            'cash' => 'نقدي',
            'card' => 'بطاقة',
            'bank' => 'تحويل بنكي',
        ];
        return $labels[$this->payment_method] ?? $this->payment_method;
    }

    public function getRecurringTypeLabelAttribute()
    {
        $labels = [
            'daily' => 'يومي',
            'weekly' => 'اسبوعي',
            'monthly' => 'شهري',
        ];
        return $labels[$this->recurring_type] ?? $this->recurring_type;
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'paid' => 'مدفوع',
            'pending' => 'معلق',
        ];
        return $labels[$this->status] ?? $this->status;
    }
}
