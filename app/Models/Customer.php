<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers'; // optional if your table name matches the model name

    protected $fillable = [
        'CustomerName',
        'Phone',
        'Address',
        'SyncStatus',
        'IsDeleted',
    ];

    public $timestamps = true; // Enable timestamps

    const CREATED_AT = 'CreatedAt';
    const UPDATED_AT = 'UpdatedAt';


    protected $casts = [
        'IsDeleted' => 'boolean',
    ];


    public function salesInvoice()
    {
        return $this->hasMany(SalesInvoice::class, 'CustomerID');
    }

    public function customerLedger()
    {
        return $this->hasMany(CustomerLedger::class, 'CustomerID');
    }
}
