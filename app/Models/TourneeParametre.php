<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TourneeParametre extends Model
{
    protected $table = 'tournee_parametres';

    protected $fillable = [
        'site_id',
        'jours_actifs',
        'heure_debut',
        'heure_fin',
        'creneaux',
        'delai_min_heures',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'jours_actifs'     => 'array',
        'creneaux'         => 'array',
        'is_active'        => 'boolean',
        'delai_min_heures' => 'integer',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    // Vérifie si un jour donné est actif
    public function isJourActif(Carbon $date): bool
    {
        $jours = [
            0 => 'dimanche', 1 => 'lundi', 2 => 'mardi',
            3 => 'mercredi', 4 => 'jeudi', 5 => 'vendredi', 6 => 'samedi',
        ];
        return in_array($jours[$date->dayOfWeek], $this->jours_actifs ?? []);
    }

    // Créneau suggéré selon l'heure actuelle
    // Si tous les créneaux d'aujourd'hui sont passés → vérifier demain
    public function getCrenauSuggere(Carbon $now = null): ?array
    {
        $now      = $now ?? Carbon::now();
        $creneaux = $this->creneaux ?? [];
        $heure    = $now->format('H:i');

        // Nuit ou très tôt le matin → suggérer premier créneau aujourd'hui
        if ($heure < '08:00') {
            return $creneaux[0] ?? null;
        }

        // Chercher le prochain créneau disponible aujourd'hui
        foreach ($creneaux as $creneau) {
            $debut  = $now->copy()->startOfDay()->setTimeFromTimeString($creneau['debut']);
            $limite = $debut->copy()->subHours($this->delai_min_heures);
            if ($now->lessThan($limite)) {
                return $creneau;
            }
        }

        // Tous les créneaux d'aujourd'hui sont passés → proposer demain
        // Vérifier si demain est un jour actif
        $demain = $now->copy()->addDay();
        if ($this->isJourActif($demain)) {
            // Retourner le premier créneau de demain
            return $creneaux[0] ?? null;
        }

        // Demain est fermé → chercher le prochain jour ouvert
        for ($i = 2; $i <= 7; $i++) {
            $prochainJour = $now->copy()->addDays($i);
            if ($this->isJourActif($prochainJour)) {
                return $creneaux[0] ?? null;
            }
        }

        return null;
    }

    // Tous les créneaux encore disponibles pour la date donnée
    public function getCreneauxDisponibles(Carbon $now = null): array
    {
        $now      = $now ?? Carbon::now();
        $creneaux = $this->creneaux ?? [];
        $heure    = $now->format('H:i');

        // Très tôt le matin → tous disponibles aujourd'hui
        if ($heure < '08:00') {
            return $creneaux;
        }

        // Filtrer créneaux encore disponibles aujourd'hui
       $delai = $this->delai_min_heures;
$disponibles = array_values(array_filter($creneaux, function ($c) use ($now, $delai) {
    $debut  = $now->copy()->startOfDay()->setTimeFromTimeString($c['debut']);
    $limite = $debut->copy()->subHours($delai);
    return $now->lessThan($limite);
}));

        return $disponibles; // peut être vide si tous passés
    }
}