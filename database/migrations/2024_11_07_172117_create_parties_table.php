<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration pour faire la table des parties
 */
return new class extends Migration
{
    /**
     * Exécute la migration pour faire la table "parties"
     */
    public function up(): void
    {
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('nb_participants');
            $table->boolean('termine')->default(false);
            $table->foreignId('createur_id')->constrained('utilisateurs');
            $table->foreignId('gagnant_id')->nullable()->constrained('utilisateurs');
            $table->timestamps();
        });
    }

    /**
     * Retire la table "parties"
     */
    public function down(): void
    {
        Schema::dropIfExists('parties');
    }
};
