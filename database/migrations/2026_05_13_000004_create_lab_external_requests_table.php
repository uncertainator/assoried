<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_external_requests', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['citoyen', 'entreprise']);
            $table->string('nom_contact');
            $table->string('email');
            $table->string('telephone')->nullable();
            $table->string('raison_sociale')->nullable();
            $table->string('territoire')->nullable();
            $table->string('besoin_type')->nullable();
            $table->text('message');
            $table->enum('statut', ['nouvelle', 'en_cours', 'traitee'])->default('nouvelle');
            $table->boolean('rgpd_consent')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_external_requests');
    }
};
