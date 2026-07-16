<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TourneeException extends Model
{
    protected $table = 'tournee_exceptions';

    protected $fillable = [
        'site_id',
        'date',
        'label',
        'is_closed',
        'creneaux_custom',
        'notes',
    ];

    protected $casts = [
        'date'           => 'date',
        'is_closed'      => 'boolean',
        'creneaux_custom' => 'array',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Trouve l'exception pour un site et une date donnée
     * Cherche d'abord une exception spécifique au site, puis une exception globale
     */
    public static function findForSiteAndDate(int $siteId, Carbon $date): ?self
    {
        // Exception spécifique au site
        $exception = self::where('site_id', $siteId)
            ->whereDate('date', $date->toDateString())
            ->first();

        if ($exception) return $exception;

        // Exception globale (tous les sites)
        return self::whereNull('site_id')
            ->whereDate('date', $date->toDateString())
            ->first();
    }
}