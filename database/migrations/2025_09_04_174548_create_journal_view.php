<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW journal AS
            SELECT
                cl.id AS TransactionID,
                cl.CustomerID AS CustomerID,
                cl.LedgerDate AS TransactionDate,
                cl.Description AS Description,
                CASE WHEN cl.TransactionType = 'Debit' AND cl.ReferenceType <> 'invoice' THEN cl.Amount ELSE 0 END AS MoneyOut,
                CASE WHEN cl.TransactionType = 'Credit' THEN cl.Amount ELSE 0 END AS MoneyIn,
                'customer_ledger' AS SourceType,
                cl.ReferenceID AS ReferenceID,
                cl.ReferenceType AS ReferenceType
            FROM customer_ledgers cl
            WHERE cl.IsDeleted = 0

            UNION ALL

            SELECT
                e.id AS TransactionID,
                NULL AS CustomerID,
                e.ExpenseDate AS TransactionDate,
                e.Description AS Description,
                e.Amount AS MoneyOut,
                0 AS MoneyIn,
                'expense' AS SourceType,
                NULL AS ReferenceID,
                NULL AS ReferenceType
            FROM expenses e
            WHERE e.IsDeleted = 0

            UNION ALL

            SELECT
                si.id AS TransactionID,
                si.CustomerID AS CustomerID,
                si.InvoiceDate AS TransactionDate,
                si.Description AS Description,
                0 AS MoneyOut,
                si.RecievedAmount AS MoneyIn,
                'sales_invoice' AS SourceType,
                NULL AS ReferenceID,
                NULL AS ReferenceType
            FROM sales_invoices si
            WHERE si.IsDeleted = 0
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS journal");
    }
};
