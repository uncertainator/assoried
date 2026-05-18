<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 255);
            $table->text('description')->nullable();
            $table->dateTime('date_cloture')->nullable();
            $table->string('mode_recueil', 30);
            $table->json('options')->nullable();
            $table->boolean('masque')->default(false);
            $table->timestamps();

            $table->index('mode_recueil');
            $table->index('date_cloture');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
