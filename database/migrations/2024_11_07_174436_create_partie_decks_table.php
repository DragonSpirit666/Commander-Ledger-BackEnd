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
        Schema::create('partie_decks', function (Blueprint $table) {
            $table->id();
            $table->integer('position')->nullable();
            $table->boolean('validee')->default(false);
            $table->foreignId('partie_id')->constrained('parties');
            $table->foreignId('deck_id')->constrained('decks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partie_decks');
    }
};
