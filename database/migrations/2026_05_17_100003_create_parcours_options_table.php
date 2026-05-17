<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parcours_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('parcours_questions')->cascadeOnDelete();
            $table->string('label');
            // Exactly one of next_question_id or service_id should be set.
            // Neither = unconfigured branch → fallback message shown to visitor.
            $table->foreignId('next_question_id')->nullable()->constrained('parcours_questions')->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('parcours_services')->nullOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['question_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcours_options');
    }
};
