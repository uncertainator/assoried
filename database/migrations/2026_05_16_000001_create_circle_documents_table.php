<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('circle_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->string('title', 255);
            $table->enum('type', ['pdf', 'link']);
            $table->date('document_date');
            $table->json('tags')->nullable();
            $table->text('description')->nullable();
            $table->string('url', 2048)->nullable();
            $table->string('file_path')->nullable();
            $table->string('original_filename')->nullable();
            $table->timestamps();

            $table->index(['circle_id', 'document_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('circle_documents');
    }
};
