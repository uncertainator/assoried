<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('circle_journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 255);
            $table->text('content');
            $table->date('entry_date');
            $table->softDeletes();
            $table->timestamps();
            $table->index(['circle_id', 'entry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('circle_journal_entries');
    }
};
