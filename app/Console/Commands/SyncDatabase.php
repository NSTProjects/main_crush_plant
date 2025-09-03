<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SyncDatabase extends Command
{
    // The name and signature of the console command
    protected $signature = 'sync:database';

    // The console command description
    protected $description = 'Sync database tables that are not marked as synced with an external server';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Tables to sync
        $tablesToSync = [
            'customers',
            'customer_ledgers',
            'deliveries',
            'expenses',
            'products',
            'sales_invoices',
            'sales_invoice_items',
        ];

        $payload = [];

        foreach ($tablesToSync as $table) {
            // Query the database for rows where sync_status != 'synced'
            $rows = DB::table($table)->where('SyncStatus', '!=', 'synced')->get();

            if ($rows->isNotEmpty()) {
                $payload[] = [
                    'table' => $table,
                    'data' => $rows
                ];
            }
        }

        // If there is no data to sync
        if (empty($payload)) {
            $this->info('⏹️ No data to sync in any table.');
            return;
        }

        // Send data to external server
        $response = Http::post('https://noorantech.com/sync_api.php', $payload);

        if ($response->failed()) {
            $this->error('❌ Failed to connect to the server.');
            return;
        }

        $result = $response->json();

        if ($result && $result['status'] === 'success') {
            $this->info('✅ Syncing all tables was successful.');

            // Update sync_status to 'synced' for each table
            foreach ($payload as $entry) {
                $table = $entry['table'];
                $ids = array_column($entry['data']->toArray(), 'TenantID'); // Change to the correct ID based on the table

                if (!empty($ids)) {
                    DB::table($table)->whereIn('TenantID', $ids)->update(['sync_status' => 'synced']);
                }
            }
        } else {
            $this->error('❌ Error in server response:');
            $this->line($response->body());
        }
    }
}
