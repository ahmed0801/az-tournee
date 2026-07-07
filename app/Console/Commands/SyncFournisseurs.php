<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Site;
use App\Models\Fournisseur;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncFournisseurs extends Command
{
    protected $signature   = 'tournee:sync-fournisseurs {--site= : Slug du site spécifique}';
    protected $description = 'Synchronise les fournisseurs depuis tous les sites aznegoce';

    public function handle()
    {
        $query = Site::where('is_active', true);

        if ($this->option('site')) {
            $query->where('slug', $this->option('site'));
        }

        $sites = $query->get();

        if ($sites->isEmpty()) {
            $this->error('Aucun site actif trouvé.');
            return 1;
        }

        $this->info('🔄 Synchronisation des fournisseurs...');
        $this->newLine();

        $totalSynced = 0;
        $errors      = 0;

        foreach ($sites as $site) {
            $this->output->write("  → {$site->name} ({$site->url}) ... ");

            try {
                $response = Http::timeout(15)
                    ->withHeaders([
                        'X-API-KEY' => $site->api_key,
                        'Accept'    => 'application/json',
                    ])
                    ->get($site->url . '/api/tournee/fournisseurs-list');

                if (!$response->successful()) {
                    $this->output->writeln('<error>❌ HTTP ' . $response->status() . '</error>');
                    $errors++;
                    continue;
                }

                $fournisseurs = $response->json();
                $count = 0;

                foreach ($fournisseurs as $f) {
                    Fournisseur::updateOrCreate(
                        ['site_id' => $site->id, 'remote_id' => $f['id']],
                        [
                            'name'    => $f['name'],
                            'address' => isset($f['address']) ? $f['address'] : null,
                            'city'    => isset($f['city'])    ? $f['city']    : null,
                            'phone'   => isset($f['phone'])   ? $f['phone']   : null,
                        ]
                    );
                    $count++;
                }

                $totalSynced += $count;
                $this->output->writeln('<info>✅ ' . $count . ' fournisseurs</info>');
                Log::info("Sync fournisseurs {$site->name}: {$count} fournisseurs");

            } catch (\Exception $e) {
                $this->output->writeln('<error>❌ ' . $e->getMessage() . '</error>');
                Log::error("Sync fournisseurs {$site->name}: " . $e->getMessage());
                $errors++;
            }
        }

        $this->newLine();
        $this->info("✅ Total synchronisé : {$totalSynced} fournisseurs");

        if ($errors > 0) {
            $this->warn("⚠️  {$errors} site(s) en erreur — vérifiez les logs.");
        }

        // Mettre à jour le cache de la dernière sync
        cache(['last_sync_at' => now()->format('d/m/Y à H:i')], 86400);

        return 0;
    }
}