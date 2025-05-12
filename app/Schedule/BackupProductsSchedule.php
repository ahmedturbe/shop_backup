<?php

namespace App\Schedule;

use App\Jobs\BackupProductsJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application;

class BackupProductsSchedule extends ServiceProvider
{
    public function schedule(Schedule $schedule, Application $app): void
    {
        // Start backup every day at 2:00 AM
       // $schedule->job(new BackupProductsJob())->dailyAt('02:00');
        //For testing
        $schedule->job(new BackupProductsJob())->everyMinute();
    }
}
