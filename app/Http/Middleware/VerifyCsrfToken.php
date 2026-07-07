<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Routes chauffeur (interface mobile Panasonic)
        'chauffeur/scan',
        'chauffeur/scan/confirm',
        'chauffeur/probleme',
        'chauffeur/self-assign',
        'chauffeur/logout',
 
        // Routes API (protégées par X-API-KEY)
        'api/tournee/*',
    ];
}
