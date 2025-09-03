<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoiceItem extends Model
{
    public function salseInoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'InvoiceID');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductID');
    }


    use HasFactory;

    protected $table = 'sales_invoice_items'; // optional if your table name matches the model name

    protected $fillable = [
        'InvoiceID',
        'ProductID',
        'Quantity',
        'UnitPrice',
        'TotalPrice',
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
