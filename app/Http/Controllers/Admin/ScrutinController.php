<?php

namespace App\Http\Controllers\Admin;

use App\Data\CreateScrutinData;
use App\Enums\ScrutinMajorityType;
use App\Enums\ScrutinQuorumType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CancelScrutinRequest;
use App\Http\Requests\Admin\CloseScrutinRequest;
use App\Http\Requests\Admin\PublishScrutinRequest;
use App\Http\Requests\Admin\StoreScrutinRequest;
use App\Http\Requests\Admin\UpdateScrutinRequest;
use App\Models\Scrutin;
use App\Services\ScrutinService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ScrutinController extends Controller
{
    public function __construct(private ScrutinService $service) {}

    public function index(): View
    {
        $scrutins = Scrutin::with('creator')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.scrutins.index', compact('scrutins'));
    }

    public function create(): View
    {
        $this->authorize('create', Scrutin::class);

        return view('admin.scrutins.create');
    }

    public function store(StoreScrutinRequest $request): RedirectResponse
    {
        $scrutin = $this->service->create($this->buildData($request));

        return redirect()->route('admin.scrutins.show', $scrutin)
            ->with('success', 'Scrutin créé en brouillon.');
    }

    public function show(Scrutin $scrutin): View
    {
        if ($scrutin->isExpired()) {
            $this->service->close($scrutin, null);
            $scrutin->refresh();
        }

        $scrutin->load(['options', 'votes.user', 'votes.option', 'winningOption']);

        return view('admin.scrutins.show', compact('scrutin'));
    }

    public function edit(Scrutin $scrutin): View
    {
        $this->authorize('update', $scrutin);

        $scrutin->load('options');

        return view('admin.scrutins.edit', compact('scrutin'));
    }

    public function update(UpdateScrutinRequest $request, Scrutin $scrutin): RedirectResponse
    {
        $this->service->update($scrutin, $this->buildData($request));

        return redirect()->route('admin.scrutins.show', $scrutin)
            ->with('success', 'Scrutin mis à jour.');
    }

    public function publish(PublishScrutinRequest $request, Scrutin $scrutin): RedirectResponse
    {
        $this->service->publish($scrutin);

        return redirect()->route('admin.scrutins.show', $scrutin)
            ->with('success', 'Scrutin publié. Les membres peuvent désormais voter.');
    }

    public function close(CloseScrutinRequest $request, Scrutin $scrutin): RedirectResponse
    {
        $this->service->close($scrutin, $request->user());

        return redirect()->route('admin.scrutins.show', $scrutin)
            ->with('success', 'Scrutin clôturé manuellement.');
    }

    public function cancel(CancelScrutinRequest $request, Scrutin $scrutin): RedirectResponse
    {
        $this->service->cancel($scrutin);

        return redirect()->route('admin.scrutins.show', $scrutin)
            ->with('success', 'Scrutin annulé.');
    }

    private function buildData(StoreScrutinRequest|UpdateScrutinRequest $request): CreateScrutinData
    {
        $validated = $request->validated();

        $options = collect($validated['options'])
            ->values()
            ->map(fn ($opt, $i) => [
                'label' => $opt['label'],
                'position' => (int) ($opt['position'] ?? ($i + 1)),
            ])
            ->toArray();

        return new CreateScrutinData(
            title: $validated['title'],
            description: $validated['description'] ?? null,
            opened_at: $validated['opened_at'],
            closes_at: $validated['closes_at'],
            quorum_type: ScrutinQuorumType::from($validated['quorum_type']),
            quorum_value: (float) $validated['quorum_value'],
            majority_type: ScrutinMajorityType::from($validated['majority_type']),
            majority_threshold: isset($validated['majority_threshold'])
                ? (float) $validated['majority_threshold']
                : null,
            options: $options,
            created_by: $request->user()->id,
        );
    }
}
