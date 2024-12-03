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
        Schema::table('decks', function (Blueprint $table) {
            $table->dropColumn('pourcentage_cartes_jaunes');
        });

        Schema::table('decks', function (Blueprint $table) {
            // Add the new column with the desired attributes
            $table->double('pourcentage_cartes_sans_couleur')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('decks', function (Blueprint $table) {
            $table->dropColumn('pourcentage_cartes_sans_couleur');
        });

        Schema::table('decks', function (Blueprint $table) {
            $table->double('pourcentage_cartes_jaunes')->default(0);
        });
    }
};
