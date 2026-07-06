<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fournisseur extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id', 'remote_id', 'name', 'address', 'city', 'phone',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function tourneeLines()
    {
        return $this->hasMany(TourneeLine::class);
    }
}