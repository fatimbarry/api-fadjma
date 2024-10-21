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
        Schema::create('ventes', function (Blueprint $table) {
            $table->id(); // Colonne 'id' pour identifier chaque vente
            $table->unsignedBigInteger('client_id')->nullable(); // Rendre 'client_id' nullable
            $table->date('date_vente'); // Date de la vente
            $table->decimal('montant_total', 10, 2); // Montant total de la vente
            $table->timestamps(); // Colonnes 'created_at' et 'updated_at' pour suivre les dates de création et de mise à jour

            // Clé étrangère pour lier à la table 'clients' (si applicable)
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventes');
    }
};
