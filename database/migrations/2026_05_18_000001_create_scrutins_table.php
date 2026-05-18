<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scrutins', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->dateTime('opened_at')->nullable();
            $table->dateTime('closes_at')->nullable();
            $table->enum('quorum_type', ['fixed', 'proportional']);
            $table->decimal('quorum_value', 8, 2);
            $table->enum('majority_type', ['simple', 'qualified']);
            $table->decimal('majority_threshold', 5, 2)->nullable();
            $table->enum('status', ['draft', 'open', 'closed', 'cancelled'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            // Résultat (renseigné à la clôture)
            $table->enum('result_status', ['quorum_not_reached', 'adopted', 'no_decision'])->nullable();
            $table->unsignedInteger('total_votes')->nullable();
            // winning_option_id : FK ajoutée dans la migration suivante (dépendance circulaire)
            $table->unsignedBigInteger('winning_option_id')->nullable();
            $table->unsignedInteger('active_members_at_close')->nullable();
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scrutins');
    }
};
