<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{

    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductID');
    }

    use HasFactory;

    protected $table = 'deliveries'; // optional if your table name matches the model name

    protected $fillable = [
        'DeliveryDate',
        'ProductID',
        'Vehicle',
        'NumOfTrucks',
        'CubicMetersPerTruck',
        'TotalVolume',
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
