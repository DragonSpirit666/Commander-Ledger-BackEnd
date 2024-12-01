<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lance la migration des decks
     */
    public function up(): void
    {
        Schema::create('decks', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->string('photo')->nullable();
            $table->string('cartes');
            $table->integer('nb_parties_gagnees')->default(0);
            $table->integer('nb_parties_perdues')->default(0);
            $table->double('prix')->default(0);
            $table->double('salt')->nullable();
            $table->double('pourcentage_utilisation');
            $table->bigInteger('utilisateur_id')->unsigned();
            $table->double('pourcentage_cartes_bleues')->default(0);
            $table->double('pourcentage_cartes_sans_couleur')->default(0);
            $table->double('pourcentage_cartes_rouges')->default(0);
            $table->double('pourcentage_cartes_noires')->default(0);
            $table->double('pourcentage_cartes_vertes')->default(0);
            $table->double('pourcentage_cartes_blanches')->default(0);
            $table->boolean('supprime')->default(false);
            $table->foreign('utilisateur_id')
                ->references('id')
                ->on('utilisateurs')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Détruit la table de migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('decks');
    }
};
