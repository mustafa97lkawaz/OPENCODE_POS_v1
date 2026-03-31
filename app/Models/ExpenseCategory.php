<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'Category_name',
        'Description',
        'Created_by',
    ];

    public function expenses()
    {
        return $this->hasMany('App\Models\Expense');
    }
}