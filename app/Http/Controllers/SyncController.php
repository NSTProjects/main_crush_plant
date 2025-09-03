<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    public function sync(Request $request)
    {
        $data = $request->input('data'); // Get the payload data

        foreach ($data as $entry) {
            $table = $entry['table'];
            $rows = $entry['data'];

            // Insert data into the table
            foreach ($rows as $row) {
                DB::table($table)->updateOrInsert(
                    ['TenantID' => $row['TenantID']], // Unique identifier to avoid duplicates
                    $row // The data to insert or update
                );
            }
        }

        return response()->json(['status' => 'success']);
    }
}
