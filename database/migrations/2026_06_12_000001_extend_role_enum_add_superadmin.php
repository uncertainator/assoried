<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Widen the `role` enum to include 'superadmin'. Driver-aware: MySQL needs a
        // MODIFY; SQLite stores enums as varchar + CHECK, rebuilt natively via change().
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['superadmin', 'admin', 'referent', 'adherent'])
                    ->default('adherent')->change();
            });

            return;
        }

        DB::statement("ALTER TABLE users MODIFY role ENUM('superadmin', 'admin', 'referent', 'adherent') NOT NULL DEFAULT 'adherent'");
    }

    public function down(): void
    {
        // Demote any superadmin first, otherwise the narrowed enum/CHECK rejects the row.
        DB::table('users')->where('role', 'superadmin')->update(['role' => 'admin']);

        if (DB::getDriverName() === 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'referent', 'adherent'])
                    ->default('adherent')->change();
            });

            return;
        }

        DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'referent', 'adherent') NOT NULL DEFAULT 'adherent'");
    }
};
