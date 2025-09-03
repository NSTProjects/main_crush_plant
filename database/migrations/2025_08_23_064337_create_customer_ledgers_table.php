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
        Schema::create('customer_ledgers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('CustomerID');
            $table->date('LedgerDate');
            $table->text('Description')->nullable();
            $table->enum('TransactionType', ['Debit', 'Credit'])->default('Debit');
            $table->integer('Amount');
            $table->unsignedBigInteger('ReferenceID')->nullable();
            $table->enum('ReferenceType', ['invoice', 'payment_in', 'payment_out'])->default('invoice');
            $table->enum('SyncStatus', ['pending', 'synced', 'conflict'])->default('pending');
            $table->boolean('IsDeleted')->default(false);
            $table->timestamp('CreatedAt')->useCurrent();
            $table->timestamp('UpdatedAt')->useCurrent()->useCurrentOnUpdate();

            // Foreign key constraint
            $table->foreign('CustomerID')->references('id')->on('customers');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_ledgers');
    }
};
