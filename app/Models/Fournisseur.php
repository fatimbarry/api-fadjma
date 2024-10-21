<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Fournisseur extends Model
{
    protected $table= 'fournisseurs';
    protected $fillable =
    ['nom',
    'prenom',
    'email',
    'telephone'
    ];
    use HasFactory;

    // Define the relationship with Medicament
    public function medicaments(): BelongsToMany
    {
        return $this->belongsToMany(Medicament::class, 'fournisseur_medicament')
            ->withPivot('prix', 'quantite')
            ->withTimestamps();
    }
}
