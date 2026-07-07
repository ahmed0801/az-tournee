<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chauffeur extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'phone', 'email', 'password', 'is_active',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tourneeLines()
    {
        return $this->hasMany(TourneeLine::class);
    }

    public function lignesAujourdhui()
    {
        return $this->tourneeLines()->whereDate('date_tournee', today());
    }

    public function lignesMatin()
    {
        return $this->lignesAujourdhui()->where('slot', 'matin');
    }

    public function lignesApresMidi()
    {
        return $this->lignesAujourdhui()->where('slot', 'apres_midi');
    }
}