<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Registering the SyncDatabase command
        \App\Console\Commands\SyncDatabase::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Schedule the 'sync:database' command to run every hour
        $schedule->command('sync:database')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        // Load all commands in the 'app/Console/Commands' directory
        $this->load(__DIR__ . '/Commands');

        // Load the routes for console commands (if any)
        require base_path('routes/console.php');
    }
}
