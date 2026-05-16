<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            // MySQL ne supporte pas les index partiels filtrés : l'unicité du CR
            // publié par réunion est enforced applicativement dans PublishMeetingReportAction.
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->text('participants')->nullable();
            $table->json('agenda_notes')->nullable();
            $table->json('decisions')->nullable();
            $table->json('open_points')->nullable();
            $table->text('free_notes')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['meeting_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_reports');
    }
};
