<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Backup\ShopBackupService;
use App\Services\Backup\BackupProcessor;

class BackupServiceCommand extends Command
{
    protected $signature = 'backup:process';
    protected $description = 'Fetch and store product data from Shop service.';

    public function handle(ShopBackupService $shopService): void
    {
        $this->info('Starting backup process...');

        try {
            $shopService->run();
            $this->info('Backup process completed successfully.');
        } catch (\Exception $e) {
            $this->error("Error during backup: " . $e->getMessage());
        }
    }
}
