<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TourneeLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id', 'source_type', 'source_id', 'source_numdoc', 'source_line_id',
        'article_code', 'article_name', 'quantity', 'barcode',
        'fournisseur_id', 'fournisseur_name',
        'chauffeur_id', 'date_tournee', 'slot', 'statut',
        'scanned_barcode', 'scanned_at', 'probleme_notes',
        'notes', 'created_by_name',
    ];

    protected $casts = [
        'date_tournee' => 'date',
        'scanned_at'   => 'datetime',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class);
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function barcodeHistory()
    {
        return $this->hasMany(BarcodeHistory::class);
    }

    // ── Accesseurs PHP 7.4 (pas de match()) ───────────────────

    public function getStatutColorAttribute()
    {
        $colors = [
            'en_attente' => 'secondary',
            'assigné'    => 'primary',
            'en_route'   => 'info',
            'recupere'   => 'success',
            'au_magasin' => 'dark',
            'probleme'   => 'danger',
        ];
        return $colors[$this->statut] ?? 'secondary';
    }

    public function getStatutIconAttribute()
    {
        $icons = [
            'en_attente' => 'fas fa-clock',
            'assigné'    => 'fas fa-user-check',
            'en_route'   => 'fas fa-car',
            'recupere'   => 'fas fa-check-circle',
            'au_magasin' => 'fas fa-store',
            'probleme'   => 'fas fa-exclamation-triangle',
        ];
        return $icons[$this->statut] ?? 'fas fa-circle';
    }

    public function getStatutLabelAttribute()
    {
        $labels = [
            'en_attente' => 'En attente',
            'assigné'    => 'Assigné',
            'en_route'   => 'En route',
            'recupere'   => 'Récupéré',
            'au_magasin' => 'Au magasin',
            'probleme'   => 'Problème',
        ];
        return $labels[$this->statut] ?? $this->statut;
    }

    public function getSlotLabelAttribute()
    {
        return $this->slot === 'matin'
            ? '🌅 Matin (8h-12h)'
            : '🌇 Après-midi (13h-18h)';
    }

    // ── Scopes ─────────────────────────────────────────────────

    public function scopeToday($query)
    {
        return $query->whereDate('date_tournee', today());
    }

    public function scopeMatin($query)
    {
        return $query->where('slot', 'matin');
    }

    public function scopeApresMidi($query)
    {
        return $query->where('slot', 'apres_midi');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopePourChauffeur($query, $chauffeurId)
    {
        return $query->where('chauffeur_id', $chauffeurId);
    }
}