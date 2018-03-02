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
        'App\Console\Commands\InitAttendance',
        'App\Console\Commands\GetInfo',
        'App\Console\Commands\SalesHistory',
        'App\Console\Commands\UpdateApm',
        'App\Console\Commands\testImport',
        'App\Console\Commands\ImportPrice',
        'App\Console\Commands\ImportLeadtime',
        'App\Console\Commands\ImportTimeGone',
        'App\Console\Commands\ImportProductFocus',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('init:attendance')->monthlyOn(1);
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
