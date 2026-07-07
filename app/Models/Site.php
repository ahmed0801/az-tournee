<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'url', 'api_key',
        'address', 'city', 'phone', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tourneeLines()
    {
        return $this->hasMany(TourneeLine::class);
    }

    public function fournisseurs()
    {
        return $this->hasMany(Fournisseur::class);
    }
}