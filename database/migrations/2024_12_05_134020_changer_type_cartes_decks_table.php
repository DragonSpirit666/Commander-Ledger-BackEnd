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
            $table->dropColumn('cartes');
        });

        Schema::table('decks', function (Blueprint $table) {
            $table->text('cartes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('decks', function (Blueprint $table) {
            $table->dropColumn('cartes');
        });
    }
};
