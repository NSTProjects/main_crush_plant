<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    // The name and signature of the console command.
    protected $signature = 'backup:database';

    // The console command description.
    protected $description = 'Backup MySQL database using mysqldump and gzip compression';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Database credentials and settings
        $user = env('DB_USERNAME', 'root');
        $pass = env('DB_PASSWORD', '');
        $host = env('DB_HOST', 'localhost');
        $database = 'laravel-amin'; // Your database name
        $mysqldump = 'C:/xampp/mysql/bin/mysqldump.exe'; // Path to mysqldump (adjust for your system)

        // Define the backup directory
        // $baseDir = storage_path('D:/db_backups/' . now()->format('Y-m-d'));
        // Define the backup directory on D drive
        $baseDir = 'D:/db_backups/' . now()->format('Y-m-d');

        if (!is_dir($baseDir) && !mkdir($baseDir, 0777, true)) {
            $this->error("Cannot create backup directory: $baseDir");
            return;
        }

        $timestamp = now()->format('Ymd_His');
        $gzFilePath = $baseDir . "/{$database}_{$timestamp}.sql.gz";
        $errFilePath = $baseDir . "/{$database}_{$timestamp}.err.log";

        // Build the mysqldump command
        $cmd = sprintf(
            '"%s" --user=%s --password=%s --host=%s --default-character-set=utf8mb4 --single-transaction --quick --routines --triggers --events --hex-blob --skip-comments %s',
            $mysqldump,
            escapeshellarg($user),
            escapeshellarg($pass),
            escapeshellarg($host),
            escapeshellarg($database)
        );

        // Define the descriptors for the process
        $descriptors = [
            0 => ['pipe', 'r'],  // stdin
            1 => ['pipe', 'w'],  // stdout (SQL dump)
            2 => ['file', $errFilePath, 'w'] // stderr -> error log
        ];

        // Start the process
        $process = proc_open($cmd, $descriptors, $pipes);
        if (!is_resource($process)) {
            $this->error("Failed to start mysqldump for {$database}");
            return;
        }

        // Open gzip file to write the dump data
        $gz = gzopen($gzFilePath, 'wb9'); // Level 9 for maximum compression
        if (!$gz) {
            fclose($pipes[1]);
            proc_close($process);
            $this->error("Cannot open gzip file for writing: {$gzFilePath}");
            return;
        }

        // Read from stdout and write to the gzipped file
        stream_set_blocking($pipes[1], true);
        $bytes = 0;
        while (!feof($pipes[1])) {
            $chunk = fread($pipes[1], 1024 * 1024); // Read in 1MB chunks
            if ($chunk === false) break;
            $bytes += strlen($chunk);
            gzwrite($gz, $chunk);
        }

        fclose($pipes[1]);
        gzclose($gz);

        // Close the process and check for errors
        $exitCode = proc_close($process);
        if ($exitCode !== 0) {
            // If the dump failed, delete the partial file and log the error
            @unlink($gzFilePath);
            $this->error("Dump failed for {$database}. See error log: {$errFilePath}");
        } else {
            // If successful, check and clean up the error log if empty
            if (file_exists($errFilePath) && filesize($errFilePath) === 0) {
                @unlink($errFilePath);
            }

            // Output the result
            $mb = number_format($bytes / (1024 * 1024), 2); // File size in MB
            $this->info("Backup completed for {$database} -> {$gzFilePath} ({$mb} MB uncompressed, gzipped)");
        }
    }
}
