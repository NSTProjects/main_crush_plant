<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products'; // optional if your table name matches the model name

    protected $fillable = [
        'ProductName',
        'OpenStock',
        'Unit',
        'UnitPrice',
        'SyncStatus',
        'IsDeleted',
    ];

    public $timestamps = true; // Enable timestamps

    const CREATED_AT = 'CreatedAt';
    const UPDATED_AT = 'UpdatedAt';


    protected $casts = [
        'IsDeleted' => 'boolean',
    ];

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'ProductId');
    }

     public function salesInvoiceItem()
    {
        return $this->hasMany(SalesInvoiceItem::class, 'ProductID');
    }
}
