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
        Schema::table('lab_external_requests', function (Blueprint $table) {
            $table->string('type_projet')->nullable()->after('territoire');
            $table->string('fonction')->nullable()->after('raison_sociale');
            $table->string('taille_organisation')->nullable()->after('fonction');
            $table->string('thematique')->nullable()->after('besoin_type');
        });
    }

    public function down(): void
    {
        Schema::table('lab_external_requests', function (Blueprint $table) {
            $table->dropColumn(['type_projet', 'fonction', 'taille_organisation', 'thematique']);
        });
    }
};
