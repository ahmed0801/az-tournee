<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Site;
use App\Models\Chauffeur;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 4 Sites ───────────────────────────────────────────────
        $sites = [
            ['name' => 'Conflans',  'slug' => 'conflans',  'url' => 'https://conflans.destockpa.fr',  'api_key' => 'conflans_key_2025',  'city' => 'Conflans-Sainte-Honorine'],
            ['name' => 'Épinay',   'slug' => 'epinay',    'url' => 'https://epinay.destockpa.fr',    'api_key' => 'epinay_key_2025',    'city' => 'Épinay-sur-Seine'],
            ['name' => 'Pavillons', 'slug' => 'pavillons', 'url' => 'https://pavillons.destockpa.fr', 'api_key' => 'pavillons_key_2025', 'city' => 'Les Pavillons-sous-Bois'],
            ['name' => 'Orléans',  'slug' => 'orleans',   'url' => 'https://orleans.destockpa.fr',   'api_key' => 'orleans_key_2025',   'city' => 'Orléans'],
        ];

        foreach ($sites as $site) {
            Site::firstOrCreate(['slug' => $site['slug']], array_merge($site, ['is_active' => true]));
        }
        $this->command->info('✅ 4 sites créés');

        // ── 5 Chauffeurs ──────────────────────────────────────────
        // ⚠️ Remplace les noms par les vrais noms de tes chauffeurs
        $chauffeurs = [
            'Chauffeur 1',
            'Chauffeur 2',
            'Chauffeur 3',
            'Chauffeur 4',
            'Chauffeur 5',
        ];

        foreach ($chauffeurs as $name) {
            Chauffeur::firstOrCreate(
                ['name' => $name],
                ['password' => Hash::make('tournee2025'), 'is_active' => true]
            );
        }
        $this->command->info('✅ 5 chauffeurs créés');
        $this->command->info('🔐 Mot de passe par défaut : tournee2025');
        $this->command->warn('⚠️  Changez les noms dans la table chauffeurs avec php artisan tinker');
    }
}