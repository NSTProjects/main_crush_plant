<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan as FacadesArtisan;

class BackupController extends Controller
{
    public function backupDatabase(Request $request)
    {
        // Run the backup command
        FacadesArtisan::call('backup:database');

        // Optionally, return a response after the backup is completed
        return back()->with('status', 'Database backup completed!');
    }
}
