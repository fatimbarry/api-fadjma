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
        Schema::create('inventaires', function (Blueprint $table) {
            $table->id(); // Colonne 'id' avec auto-incrémentation et clé primaire
            $table->date('date_inventaire'); // Colonne 'date_inventaire' pour stocker la date de l'inventaire
            $table->string('statut'); // Colonne 'statut' pour stocker le statut de l'inventaire
            $table->timestamps(); // Colonnes 'created_at' et 'updated_at' pour suivre les dates de création et de mise à jour
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaires');
    }
};
