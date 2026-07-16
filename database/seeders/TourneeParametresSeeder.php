<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Site;
use App\Models\TourneeParametre;
use App\Models\TourneeException;
use Carbon\Carbon;

class TourneeParametresSeeder extends Seeder
{
    public function run()
    {
        // Créneaux standard pour tous les sites
        $creneauxStandard = [
            ['label' => '9h-11h',  'debut' => '09:00', 'fin' => '11:00'],
            ['label' => '11h-12h', 'debut' => '11:00', 'fin' => '12:00'],
            ['label' => '13h-14h', 'debut' => '13:00', 'fin' => '14:00'],
            ['label' => '15h-16h', 'debut' => '15:00', 'fin' => '16:00'],
            ['label' => '17h-18h', 'debut' => '17:00', 'fin' => '18:00'],
        ];

        // Jours actifs par défaut
        $joursActifs = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];

        // Créer les paramètres pour chaque site
        $sites = Site::all();

        foreach ($sites as $site) {
            TourneeParametre::updateOrCreate(
                ['site_id' => $site->id],
                [
                    'jours_actifs'     => $joursActifs,
                    'heure_debut'      => '08:00',
                    'heure_fin'        => '18:00',
                    'creneaux'         => $creneauxStandard,
                    'delai_min_heures' => 1,
                    'is_active'        => true,
                    'notes'            => 'Configuration par défaut — ' . $site->name,
                ]
            );

            $this->command->info('✅ Paramètres créés pour : ' . $site->name);
        }

        // ── Exceptions globales — Jours fériés 2025/2026 ────────────────
        $feries = [
            // 2025
            ['date' => '2025-08-15', 'label' => 'Assomption'],
            ['date' => '2025-11-01', 'label' => 'Toussaint'],
            ['date' => '2025-11-11', 'label' => 'Armistice 1918'],
            ['date' => '2025-12-25', 'label' => 'Noël'],
            // 2026
            ['date' => '2026-01-01', 'label' => 'Jour de l\'An'],
            ['date' => '2026-04-06', 'label' => 'Lundi de Pâques'],
            ['date' => '2026-05-01', 'label' => '1er Mai — Fête du Travail'],
            ['date' => '2026-05-08', 'label' => 'Victoire 1945'],
            ['date' => '2026-05-14', 'label' => 'Ascension'],
            ['date' => '2026-05-25', 'label' => 'Lundi de Pentecôte'],
            ['date' => '2026-07-14', 'label' => 'Fête Nationale'],
            ['date' => '2026-08-15', 'label' => 'Assomption'],
            ['date' => '2026-11-01', 'label' => 'Toussaint'],
            ['date' => '2026-11-11', 'label' => 'Armistice 1918'],
            ['date' => '2026-12-25', 'label' => 'Noël'],
        ];

        foreach ($feries as $ferie) {
            TourneeException::updateOrCreate(
                ['site_id' => null, 'date' => $ferie['date']],
                [
                    'label'     => $ferie['label'],
                    'is_closed' => true,
                    'notes'     => 'Jour férié national',
                ]
            );
        }

        $this->command->info('✅ ' . count($feries) . ' jours fériés ajoutés (globaux)');
        $this->command->info('');
        $this->command->info('🎉 Seeder TourneeParametres terminé !');
    }
}