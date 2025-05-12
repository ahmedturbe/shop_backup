<?php

namespace App\Jobs;

use App\Services\Backup\ShopBackupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BackupProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        logger()->info("BackupProductsJob started");
        try {
            // Pokreće se servis koji poziva API i backupuje u lokalnu bazu
            app(ShopBackupService::class)->backup();
        } catch (\Exception $e) {
            logger()->error("BackupProductsJob failed: " . $e->getMessage());
        }
        logger()->info("BackupProductsJob finished");
    }
}
