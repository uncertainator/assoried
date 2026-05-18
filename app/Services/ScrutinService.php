<?php

namespace App\Services;

use App\Data\CreateScrutinData;
use App\Enums\ScrutinStatus;
use App\Models\Scrutin;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ScrutinService
{
    public function __construct(
        private ResultatScrutinService $resultatService,
    ) {}

    public function create(CreateScrutinData $data): Scrutin
    {
        return DB::transaction(function () use ($data) {
            $scrutin = Scrutin::create([
                'title' => $data->title,
                'description' => $data->description,
                'opened_at' => $data->opened_at,
                'closes_at' => $data->closes_at,
                'quorum_type' => $data->quorum_type,
                'quorum_value' => $data->quorum_value,
                'majority_type' => $data->majority_type,
                'majority_threshold' => $data->majority_threshold,
                'status' => ScrutinStatus::Draft,
                'created_by' => $data->created_by,
            ]);

            foreach ($data->options as $opt) {
                $scrutin->options()->create([
                    'label' => $opt['label'],
                    'position' => $opt['position'],
                ]);
            }

            return $scrutin;
        });
    }

    public function update(Scrutin $scrutin, CreateScrutinData $data): Scrutin
    {
        if (! $scrutin->isEditable()) {
            throw ValidationException::withMessages([
                'status' => 'Ce scrutin ne peut plus être modifié.',
            ]);
        }

        return DB::transaction(function () use ($scrutin, $data) {
            $scrutin->update([
                'title' => $data->title,
                'description' => $data->description,
                'opened_at' => $data->opened_at,
                'closes_at' => $data->closes_at,
                'quorum_type' => $data->quorum_type,
                'quorum_value' => $data->quorum_value,
                'majority_type' => $data->majority_type,
                'majority_threshold' => $data->majority_threshold,
            ]);

            $scrutin->options()->delete();

            foreach ($data->options as $opt) {
                $scrutin->options()->create([
                    'label' => $opt['label'],
                    'position' => $opt['position'],
                ]);
            }

            return $scrutin->fresh();
        });
    }

    public function publish(Scrutin $scrutin): Scrutin
    {
        if (! $scrutin->isEditable()) {
            throw ValidationException::withMessages([
                'status' => ['Seul un scrutin en brouillon peut être publié.'],
            ]);
        }

        if ($scrutin->options()->count() < 2) {
            throw ValidationException::withMessages([
                'options' => ['Le scrutin doit comporter au moins 2 options avant publication.'],
            ]);
        }

        if ($scrutin->closes_at === null || $scrutin->closes_at->isPast()) {
            throw ValidationException::withMessages([
                'closes_at' => ['La date de clôture doit être dans le futur.'],
            ]);
        }

        if ($scrutin->opened_at === null) {
            throw ValidationException::withMessages([
                'opened_at' => ["La date d'ouverture est obligatoire."],
            ]);
        }

        if (! $scrutin->closes_at->gt($scrutin->opened_at)) {
            throw ValidationException::withMessages([
                'closes_at' => ["La date de clôture doit être postérieure à la date d'ouverture."],
            ]);
        }

        $scrutin->update(['status' => ScrutinStatus::Open]);

        return $scrutin->fresh();
    }

    public function close(Scrutin $scrutin, ?User $closedBy = null): Scrutin
    {
        if (! $scrutin->isOpen()) {
            throw ValidationException::withMessages([
                'status' => ['Seul un scrutin ouvert peut être clôturé.'],
            ]);
        }

        return DB::transaction(function () use ($scrutin) {
            $scrutin->update(['status' => ScrutinStatus::Closed]);
            $result = $this->resultatService->compute($scrutin);

            $scrutin->update([
                'result_status' => $result->result_status,
                'total_votes' => $result->total_votes,
                'winning_option_id' => $result->winning_option_id,
                'active_members_at_close' => $result->active_members_at_close,
            ]);

            return $scrutin->fresh();
        });
    }

    public function cancel(Scrutin $scrutin): Scrutin
    {
        if (! $scrutin->canBeCancelled()) {
            throw ValidationException::withMessages([
                'status' => ['Ce scrutin ne peut pas être annulé (des votes ont déjà été enregistrés).'],
            ]);
        }

        $scrutin->update(['status' => ScrutinStatus::Cancelled]);

        return $scrutin->fresh();
    }
}
