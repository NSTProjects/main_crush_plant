<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    // public function customer()
    // {
    //     return $this->belongsTo(Customer::class, 'CustomerID');
    // }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustomerID');
    }

    use HasFactory;

    protected $table = 'sales_invoices'; // optional if your table name matches the model name

    protected $fillable = [
        'InvoiceDate',
        'CustomerID',
        'TotalAmount',
        'DiscountAmount',
        'RecievedAmount',
        'BalanceAmount',
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

     public function salesInvoiceItem()
    {
        return $this->hasMany(SalesInvoiceItem::class, 'InvoiceID');
    }
}
