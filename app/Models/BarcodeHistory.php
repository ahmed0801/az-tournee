<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BarcodeHistory extends Model
{
    use HasFactory;
    protected $table = 'barcode_history';

    protected $fillable = [
        'tournee_line_id', 'barcode_scanned',
        'article_code', 'matched', 'chauffeur_id',
    ];

    protected $casts = [
        'matched' => 'boolean',
    ];

    public function tourneeLine()
    {
        return $this->belongsTo(TourneeLine::class);
    }

    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class);
    }
}