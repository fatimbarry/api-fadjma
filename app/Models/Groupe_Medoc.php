<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groupe_Medoc extends Model
{

    use HasFactory;

    protected $table = 'grpe_medocs'; // Nom correct de la table

    protected $fillable = ['nom'];
// Relation avec le modèle Medicament
public function medicaments()
{
    return $this->hasMany(Medicament::class, 'grpemedoc_id');
}
}
