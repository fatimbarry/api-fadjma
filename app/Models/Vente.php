<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    use HasFactory;

    // Nom de la table associée au modèle
    protected $table = 'ventes';

    // Les attributs qui sont assignables en masse
    protected $fillable = [
        'client_id',
        'quantite',
        'prix_unitaire',
        'montant_total',
        'date_vente'
    ];

    // Les attributs qui doivent être cachés pour les tableaux/JSON
    //protected $hidden = [];

    // Les attributs qui doivent être convertis en types natifs
    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'total' => 'decimal:2',
        'date_vente' => 'date',
    ];

    // Relation avec le modèle Medicament
    // Dans le modèle Vente
public function medicaments()
{
    return $this->belongsToMany(Medicament::class, 'medicament_vente')
                ->withPivot('quantite', 'prix_unitaire');
}

public function client()
{
    return $this->belongsTo(Client::class);
}

public function facture()
{
    return $this->hasOne(Facture::class);
}

}
