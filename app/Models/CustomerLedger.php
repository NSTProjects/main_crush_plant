<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerLedger extends Model
{
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustomerID');
    }

    use HasFactory;

    protected $table = 'customer_ledgers'; // optional if your table name matches the model name

    protected $fillable = [
        'CustomerID',
        'LedgerDate',
        'Description',
        'TransactionType',
        'Amount',
        'ReferenceID',
        'ReferenceType',
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
