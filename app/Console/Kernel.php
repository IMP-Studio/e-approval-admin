<?php

namespace App\Console;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            $controller = new ApiController();
            $controller ->sendCheckinNotification();
        })->dailyAt('8:30')
        ->timezone('Asia/Jakarta'); 
        
        $schedule->call(function () {
            $controller = new ApiController();
            $controller ->sendCheckOutNotification();
        })->dailyAt('17:30')
        ->timezone('Asia/Jakarta');

        $schedule->command('users:mark-skipped')->dailyAt('11:00')->timezone('Asia/Jakarta');
        $schedule->command('users:auto-checkout')->dailyAt('23:59')->timezone('Asia/Jakarta');
        $schedule->command('users:auto-reject-command')->dailyAt('11:00')->timezone('Asia/Jakarta');
}

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}