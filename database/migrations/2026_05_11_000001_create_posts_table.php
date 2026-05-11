<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('body');
            $table->boolean('pushed_to_general')->default(false);
            $table->timestamp('pushed_at')->nullable();
            $table->timestamps();

            $table->index(['circle_id', 'created_at']);
            $table->index(['pushed_to_general', 'pushed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
