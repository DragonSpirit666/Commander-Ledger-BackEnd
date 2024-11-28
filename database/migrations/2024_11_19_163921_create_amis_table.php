<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Généré la migration.
     */
    public function up(): void
    {
        Schema::create('amis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('utilisateur_demandeur_id');
            $table->unsignedBigInteger('utilisateur_receveur_id');
            $table->boolean('invitation_accepter')->default(false);
            $table->timestamps();

            // Clés étrangères pour user_1_id et user_2_id
            $table->foreign('utilisateur_demandeur_id')->references('id')->on('utilisateurs')->onDelete('cascade');
            $table->foreign('utilisateur_receveur_id')->references('id')->on('utilisateurs')->onDelete('cascade');

            // Clé unique pour éviter les duplications d'amitiés
            $table->unique(['utilisateur_demandeur_id', 'utilisateur_receveur_id']);
        });
    }

    /**
     * Retour en arrière de la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('amis');
    }
};
