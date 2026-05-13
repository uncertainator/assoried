<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_internal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lab_service_id')->nullable()->constrained('lab_services')->nullOnDelete();
            $table->text('message');
            $table->date('desired_date')->nullable();
            $table->enum('status', ['nouvelle', 'en_cours', 'traitee'])->default('nouvelle');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_internal_requests');
    }
};
