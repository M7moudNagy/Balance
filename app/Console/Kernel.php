<?php

namespace App\Console;

use App\Models\Massage;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            \App\Models\Challenge::where('created_at', '<', now()->subWeek())->delete();
        })->daily();

        $schedule->command('sessions:generate-daily')->dailyAt('00:05');
        $schedule->command('sessions:send-reminder')->everyMinute();
        $schedule->command('sessions:mark-missed')->everyMinute();

        // $schedule->call(function () {
        //     Massage::where('created_at', '<', now()->subDays(30))->delete();
        // })->daily();
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
