<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('meetings', 'is_public')) {
            Schema::table('meetings', function (Blueprint $table) {
                $table->boolean('is_public')->default(false)->after('visio_url');
            });
        }
    }

    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn('is_public');
        });
    }
};
