<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'expenses'; // optional if your table name matches the model name

    protected $fillable = [
        'ExpenseDate',
        'ExpenseType',
        'Amount',
        'Description',
        'SyncStatus',
        'IsDeleted',
    ];

    public $timestamps = true; // Enable timestamps

    const CREATED_AT = 'CreatedAt';
    const UPDATED_AT = 'UpdatedAt';


    protected $casts = [
        'IsDeleted' => 'boolean',
    ];
}
