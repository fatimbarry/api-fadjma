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
        Schema::create('medicaments', function (Blueprint $table) {
            $table->id(); // Garde l'ID auto-incrémenté pour les relations internes
            $table->string('code_medicament')->unique(); // Nouveau champ pour l'ID personnalisé
            $table->unsignedBigInteger('grpemedoc_id'); // Référence au grp_medoc (optionnel)
            // Clé étrangère pour lier à la table 'clients' (si applicable)
            $table->foreign('grpemedoc_id')->references('id')->on('grpe_medocs');
            $table->string('nom');
            $table->text('description')->nullable();
            $table->string('dosage');
            $table->decimal('prix', 10, 2);
            $table->integer('stock_quantite');
            $table->string('image_path')->nullable();
            // Nouveaux champs ajoutés
            $table->string('composition');
            $table->string('fabricant');
            $table->string('type_consommation');
            $table->date('date_expiration');
            $table->text('posologie')->nullable();
            $table->text('ingredients_actifs');
            $table->text('effets_secondaires')->nullable();
            $table->string('forme_pharmaceutique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicaments');
    }
};
