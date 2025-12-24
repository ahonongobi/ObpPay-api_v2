<?php

namespace App\Console;

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

        // Schedule the monthly installment processing
        // Execute installment processing daily at midnight
        // * * * * * php /var/www/html/obppay/artisan schedule:run >> /dev/null 2>&1
        $schedule->call(function () {
            \Illuminate\Support\Facades\Http::post(env('APP_URL') . '/api/cron/installments/process');
        })->daily();

        //  Weekly loan interest (1.595%) 
        // cron:* * * * * php /path/to/your/project/artisan schedule:run >> /dev/null 2>&1
        $schedule->command('loans:apply-weekly-interest')
            ->weekly()
            ->mondays()
            ->at('00:05');

        // Daily penalty application (1.595%)
        // cron: * * * * * cd /home/obppayco/backoffice && php artisan schedule:run >> /dev/null 2>&1

        $schedule->command('loans:apply-penalty')
            ->dailyAt('00:10');
            
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
