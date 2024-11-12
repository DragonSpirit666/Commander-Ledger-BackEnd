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
            $table->integer('nb_parties_gagnees');
            $table->integer('nb_parties_perdues');
            $table->double('prix');
            $table->double('salt')->nullable();
            $table->double('pourcentage_utilisation');
            $table->boolean('supprime')->default(false);
            $table->bigInteger('utilisateur_id')->unsigned();
            $table->double('pourcentage_cartes_bleues');
            $table->double('pourcentage_cartes_jaunes');
            $table->double('pourcentage_cartes_rouges');
            $table->double('pourcentage_cartes_noires');
            $table->double('pourcentage_cartes_vertes');
            $table->double('pourcentage_cartes_blanches');
            $table->foreign('utilisateur_id')
                ->references('id')
                ->on('utilisateurs')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decks');
    }
};
