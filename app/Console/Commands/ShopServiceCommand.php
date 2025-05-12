<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Backup\ShopBackupService;


class ShopServiceCommand extends Command
{
    protected $signature = 'shop:backup';

    protected $description = 'Backupuj podatke iz Shop aplikacije putem API-ja.';

    public function handle(): void
    {
        $service = app(ShopBackupService::class);
        $service->run(); // <- Ovdje je bila greška: pozivalo se "backup()", a treba "run()"

        $this->info('Backup je uspješno završen.');
    }
}
