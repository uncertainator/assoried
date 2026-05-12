<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('circle_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->date('due_date');
            $table->enum('status', ['todo', 'in_progress', 'done'])->default('todo');
            $table->timestamps();

            $table->index(['circle_id', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('circle_actions');
    }
};
