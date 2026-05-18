<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scrutin_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scrutin_id')->constrained()->cascadeOnDelete();
            $table->string('label', 200);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
            $table->unique(['scrutin_id', 'position']);
        });

        // FK circulaire : scrutins.winning_option_id → scrutin_options.id
        // Ajoutée ici après création de la table scrutin_options
        Schema::table('scrutins', function (Blueprint $table) {
            $table->foreign('winning_option_id')
                ->references('id')
                ->on('scrutin_options')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('scrutins', function (Blueprint $table) {
            $table->dropForeign(['winning_option_id']);
        });
        Schema::dropIfExists('scrutin_options');
    }
};
