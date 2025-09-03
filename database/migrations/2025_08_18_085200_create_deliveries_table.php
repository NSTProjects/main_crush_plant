<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->date('DeliveryDate');
            $table->unsignedBigInteger('ProductID');
            $table->string('Vehicle', 100);
            $table->decimal('NumOfTrucks', 10, 2);
            $table->decimal('CubicMetersPerTruck', 10, 2);
            $table->decimal('TotalVolume', 10, 2); // OPTIMAL
            $table->text('Description')->nullable();
            $table->enum('SyncStatus', ['pending', 'synced', 'conflict'])->default('pending');
            $table->boolean('IsDeleted')->default(false);
            $table->timestamp('CreatedAt')->useCurrent();
            $table->timestamp('UpdatedAt')->useCurrent()->useCurrentOnUpdate();

            // Foreign key constraint to products.id
            $table->foreign('ProductID')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
