<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Medicament extends Model
{
    protected $fillable = [
        'code_medicament',
        'grpemedoc_id', // Référence au groupe de médicaments
        'nom',
        'description',
        'stock_quantite',
        'dosage',
        'prix',
        'image_path',
        'composition',
        'fabricant',
        'type_consommation',
        'date_expiration',
        'posologie',
        'ingredients_actifs',
        'effets_secondaires',
        'forme_pharmaceutique'
    ];

    use HasFactory;



    protected static function boot()
    {
        parent::boot();

        static::creating(function ($medicament) {
            $medicament->code_medicament = 'D' . rand(100, 999) . 'D' . rand(100000000, 999999999);
        });
    }

    // Relation avec le modèle GrpeMedoc
    public function groupe()
    {
        return $this->belongsTo(Groupe_Medoc::class, 'grpemedoc_id');
    }

    // Relation avec le modèle Vente
    public function ventes()
{
    return $this->belongsToMany(Vente::class, 'medicament_vente')
                ->withPivot('quantite', 'prix_unitaire');
}
// Define the relationship with Fournisseur
    public function fournisseurs(): BelongsToMany
    {
        return $this->belongsToMany(Fournisseur::class, 'fournisseur_medicament')
            ->withPivot('prix', 'quantite')
            ->withTimestamps();
    }
}
