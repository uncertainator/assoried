<?php

namespace App\Console\Commands;

use App\Enums\ScrutinStatus;
use App\Models\Scrutin;
use App\Services\ScrutinService;
use Illuminate\Console\Command;

class CloseExpiredScrutins extends Command
{
    protected $signature = 'app:close-expired-scrutins';

    protected $description = 'Clôture automatiquement les scrutins ouverts dont la date de clôture est dépassée';

    public function handle(ScrutinService $service): int
    {
        $expired = Scrutin::where('status', ScrutinStatus::Open)
            ->where('closes_at', '<', now())
            ->get();

        foreach ($expired as $scrutin) {
            try {
                $service->close($scrutin, closedBy: null);
                $this->info("Scrutin #{$scrutin->id} « {$scrutin->title} » clôturé automatiquement.");
            } catch (\Throwable $e) {
                $this->error("Erreur scrutin #{$scrutin->id} : {$e->getMessage()}");
                logger()->error('CloseExpiredScrutins: '.$e->getMessage(), ['scrutin_id' => $scrutin->id]);
            }
        }

        return Command::SUCCESS;
    }
}
