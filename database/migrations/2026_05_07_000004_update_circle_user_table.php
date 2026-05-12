<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $existing = DB::table('circle_user')->get();

        Schema::drop('circle_user');

        Schema::create('circle_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circle_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('joined_at')->useCurrent();
            $table->string('status', 20)->default('approved');
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->string('rejection_reason', 500)->nullable();
            $table->unique(['circle_id', 'user_id']);
        });

        foreach ($existing as $row) {
            DB::table('circle_user')->insert([
                'circle_id' => $row->circle_id,
                'user_id' => $row->user_id,
                'joined_at' => $row->joined_at,
                'status' => 'approved',
            ]);
        }
    }

    public function down(): void
    {
        $existing = DB::table('circle_user')->get();

        Schema::drop('circle_user');

        Schema::create('circle_user', function (Blueprint $table) {
            $table->foreignId('circle_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('joined_at')->useCurrent();
            $table->primary(['circle_id', 'user_id']);
        });

        foreach ($existing as $row) {
            DB::table('circle_user')->insert([
                'circle_id' => $row->circle_id,
                'user_id' => $row->user_id,
                'joined_at' => $row->joined_at,
            ]);
        }
    }
};
