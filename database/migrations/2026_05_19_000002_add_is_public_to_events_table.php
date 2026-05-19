<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    // On vérifie si la colonne n'existe pas déjà avant de tenter de l'ajouter
    if (!Schema::hasColumn('events', 'is_public')) {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_public')->default(false);
        });
    }
}

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('is_public');
        });
    }
};
