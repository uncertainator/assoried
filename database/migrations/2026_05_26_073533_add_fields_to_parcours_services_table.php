<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parcours_services', function (Blueprint $table) {
            $table->string('slug', 100)->unique()->nullable()->after('id');
            $table->text('pour_qui')->nullable()->after('use_cases');
            $table->text('ce_que_ca_produit')->nullable()->after('pour_qui');
            $table->string('format', 200)->nullable()->after('ce_que_ca_produit');
            $table->string('branche', 80)->nullable()->after('format');
        });
    }

    public function down(): void
    {
        Schema::table('parcours_services', function (Blueprint $table) {
            $table->dropColumn(['slug', 'pour_qui', 'ce_que_ca_produit', 'format', 'branche']);
        });
    }
};
