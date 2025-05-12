<?php

namespace App\Jobs;

use App\Services\Backup\ShopApiFetcher;
use App\Services\Backup\BackupProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BackupDailyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ShopApiFetcher $fetcher, BackupProcessor $processor): void
    {
        $products = $fetcher->fetch(); // fetch from source
        $processor->process($products); // process into target
    }
}
