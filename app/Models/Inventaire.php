<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventaire extends Model
{
    protected $table = 'inventaires';

    // Les attributs qui sont assignables en masse
    protected $fillable = [
        'date_inventaire',
        'statut',
    ];

    // Les attributs qui doivent être convertis en types natifs
    protected $casts = [
        'date_inventaire' => 'date',
    ];
    use HasFactory;
}

