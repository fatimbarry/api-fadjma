<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable =
    ['nom',
    'prenom',
    'email',
    'telephone'
    ];
    use HasFactory;

    public function factures()
{
    return $this->hasMany(Facture::class);
}

}
