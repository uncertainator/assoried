<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultation_reponses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')
                ->constrained('consultations')
                ->cascadeOnDelete();
            $table->string('mode', 30);
            $table->text('contenu')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('source', 20)->default('numerique');
            $table->boolean('masque')->default(false);
            $table->timestamps();

            $table->index(['consultation_id', 'ip_address', 'created_at']);
            $table->index(['consultation_id', 'masque']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_reponses');
    }
};
