<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->id(); // Crée une colonne 'id' avec auto-incrémentation et clé primaire
            $table->string('prenom');
            $table->string('nom'); // Crée une colonne 'nom' pour stocker le nom du fournisseur
            $table->string('email')->unique(); // Crée une colonne 'email' pour stocker l'email du fournisseur, avec contrainte d'unicité
            $table->string('telephone')->nullable(); // Crée une colonne 'telephone' pour stocker le numéro de téléphone du fournisseur, peut être nul
            $table->timestamps(); // Crée les colonnes 'created_at' et 'updated_at'
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fournisseurs');
    }
};