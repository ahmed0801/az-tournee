<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    // ── Commandes artisan custom ───────────────────────────────
    protected $commands = [
        \App\Console\Commands\SyncFournisseurs::class,
    ];

    // ── Planification automatique ──────────────────────────────
    protected function schedule(Schedule $schedule)
    {
        // Sync fournisseurs toutes les heures
        $schedule->command('tournee:sync-fournisseurs')
            ->hourly()
            ->appendOutputTo(storage_path('logs/sync-fournisseurs.log'));

        // Sync au démarrage de chaque journée à 6h
        $schedule->command('tournee:sync-fournisseurs')
            ->dailyAt('06:00')
            ->appendOutputTo(storage_path('logs/sync-fournisseurs.log'));
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}