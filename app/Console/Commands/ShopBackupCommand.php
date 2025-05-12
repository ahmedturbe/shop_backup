<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Backup\ShopBackupService;
use Illuminate\Support\Facades\Log;

class ShopBackupCommand extends Command
{
    protected $signature = 'shop:backup';

    protected $description = 'Poziva Shop aplikaciju i preuzima proizvode putem API-ja.';

    protected ShopBackupService $shopBackupService;

    public function __construct(ShopBackupService $shopBackupService)
    {
        parent::__construct();
        $this->shopBackupService = $shopBackupService;
    }

    public function handle(): void
    {
        $this->info('Započinjem preuzimanje proizvoda iz Shop aplikacije...');
        $this->shopBackupService->run();
        $this->info('Preuzimanje završeno.');
    }
}
