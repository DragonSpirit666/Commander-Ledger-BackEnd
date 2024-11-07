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
            $table->foreign('utilisateur_id')->references('id')->on('utilisateurs');
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
