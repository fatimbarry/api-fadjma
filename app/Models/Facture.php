<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;

    // Nom de la table associée au modèle
    protected $table = 'factures';

    // Les attributs qui sont assignables en masse
    protected $fillable = [
        'client_id',
        'date_facture',
        'montant_total',
        'numero_facture',
        'vente_id'
    ];

    // Les attributs qui doivent être cachés pour les tableaux/JSON
    protected $hidden = [];

    // Les attributs qui doivent être convertis en types natifs
    protected $casts = [
        'date_facture' => 'date',
        'montant_total' => 'decimal:2',
    ];

    // Relation avec le modèle Client (si applicable)
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function vente()
{
    return $this->belongsTo(Vente::class);
}
}
