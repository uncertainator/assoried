<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parcours_questions', function (Blueprint $table) {
            $table->boolean('show_fallback_link')->default(false)->after('is_root');
        });
    }

    public function down(): void
    {
        Schema::table('parcours_questions', function (Blueprint $table) {
            $table->dropColumn('show_fallback_link');
        });
    }
};
