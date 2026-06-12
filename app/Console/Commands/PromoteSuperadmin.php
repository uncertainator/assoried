<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;

class PromoteSuperadmin extends Command
{
    protected $signature = 'superadmin:promote {email}';

    protected $description = 'Promeut un utilisateur existant (identifié par email) au rôle superadmin (idempotent)';

    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if ($user === null) {
            $this->error("Aucun utilisateur avec l'email « {$email} ».");

            return self::FAILURE;
        }

        if ($user->role === UserRole::Superadmin) {
            $this->info("« {$email} » est déjà superadmin. Aucune modification.");

            return self::SUCCESS;
        }

        $user->role = UserRole::Superadmin;
        $user->save();

        $this->info("« {$email} » est désormais superadmin.");

        return self::SUCCESS;
    }
}
