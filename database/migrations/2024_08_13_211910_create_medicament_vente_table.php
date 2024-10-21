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
        Schema::create('medicament_vente', function (Blueprint $table) {
            $table->id(); // Identifiant unique pour chaque enregistrement
            $table->foreignId('medicament_id')
                  ->constrained('medicaments') // Référence à la table 'medicaments'
                  ->onDelete('cascade'); // Supprimer les enregistrements liés si le médicament est supprimé
            $table->foreignId('vente_id')
                  ->constrained('ventes') // Référence à la table 'ventes'
                  ->onDelete('cascade'); // Supprimer les enregistrements liés si la vente est supprimée
            $table->integer('quantite'); // Quantité du médicament vendu
            $table->decimal('prix_unitaire', 10, 2); // Prix unitaire du médicament au moment de la vente
            $table->timestamps(); // Colonnes 'created_at' et 'updated_at' pour le suivi des modifications
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicament_vente');
    }
};
