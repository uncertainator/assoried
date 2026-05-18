<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scrutin_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scrutin_id')->constrained()->cascadeOnDelete();
            $table->foreignId('scrutin_option_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['scrutin_id', 'user_id']);
            $table->index('scrutin_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scrutin_votes');
    }
};
