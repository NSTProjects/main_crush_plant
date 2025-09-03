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
        Schema::create('sales_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('InvoiceID');
            $table->unsignedBigInteger('ProductID');
            $table->decimal('Quantity', 10, 2);
            $table->integer('UnitPrice');
            $table->integer('TotalPrice');
            $table->enum('SyncStatus', ['pending', 'synced', 'conflict'])->default('pending');
            $table->boolean('IsDeleted')->default(false);
            $table->timestamp('CreatedAt')->useCurrent();
            $table->timestamp('UpdatedAt')->useCurrent()->useCurrentOnUpdate();

            // Foreign key constraint to customers.id
            $table->foreign('InvoiceID')->references('id')->on('sales_invoices');
            $table->foreign('ProductID')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoice_items');
    }
};
