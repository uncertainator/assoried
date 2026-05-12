<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 150);
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->text('description')->nullable();
            $table->string('location', 200)->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->index(['circle_id', 'starts_at']);
            $table->index('starts_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
