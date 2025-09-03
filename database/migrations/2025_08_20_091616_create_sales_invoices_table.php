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
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->date('InvoiceDate');
            $table->unsignedBigInteger('CustomerID');
            $table->integer('TotalAmount');
            $table->integer('DiscountAmount');
            $table->integer('RecievedAmount');
            $table->integer('BalanceAmount');
            $table->text('Description')->nullable();
            $table->enum('SyncStatus', ['pending', 'synced', 'conflict'])->default('pending');
            $table->boolean('IsDeleted')->default(false);
            $table->timestamp('CreatedAt')->useCurrent();
            $table->timestamp('UpdatedAt')->useCurrent()->useCurrentOnUpdate();

            // Foreign key constraint to customers.id
            $table->foreign('CustomerID')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoices');
    }
};
