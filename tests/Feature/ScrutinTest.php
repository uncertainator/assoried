<?php

namespace Tests\Feature;

use App\Enums\ScrutinMajorityType;
use App\Enums\ScrutinQuorumType;
use App\Enums\ScrutinResultStatus;
use App\Enums\ScrutinStatus;
use App\Models\Scrutin;
use App\Models\ScrutinOption;
use App\Models\ScrutinVote;
use App\Models\User;
use App\Services\ScrutinService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ScrutinTest extends TestCase
{
    use RefreshDatabase;

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Scrutin de test',
            'description' => 'Description',
            'opened_at' => now()->subHour()->format('Y-m-d H:i:s'),
            'closes_at' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'quorum_type' => 'fixed',
            'quorum_value' => 3,
            'majority_type' => 'simple',
            'majority_threshold' => null,
            'options' => [
                ['label' => 'Option A', 'position' => 1],
                ['label' => 'Option B', 'position' => 2],
            ],
        ], $overrides);
    }

    private function openScrutinWithOptions(array $scrutinState = []): array
    {
        $scrutin = Scrutin::factory()->open()->create($scrutinState);
        $optionA = ScrutinOption::factory()->create(['scrutin_id' => $scrutin->id, 'label' => 'Option A', 'position' => 1]);
        $optionB = ScrutinOption::factory()->create(['scrutin_id' => $scrutin->id, 'label' => 'Option B', 'position' => 2]);

        return [$scrutin, $optionA, $optionB];
    }

    // ----------------------------------------------------------------
    // Scénario 1 — Admin crée un scrutin (brouillon)
    // ----------------------------------------------------------------

    public function test_admin_can_create_scrutin(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.scrutins.store'), $this->validPayload());

        $response->assertRedirect();
        $this->assertDatabaseHas('scrutins', [
            'title' => 'Scrutin de test',
            'status' => ScrutinStatus::Draft->value,
            'created_by' => $admin->id,
        ]);
        $this->assertDatabaseCount('scrutin_options', 2);
    }

    // ----------------------------------------------------------------
    // Scénario 2 — Non-admin ne peut pas créer
    // ----------------------------------------------------------------

    public function test_adherent_cannot_create_scrutin(): void
    {
        $adherent = User::factory()->adherent()->create();

        $response = $this->actingAs($adherent)->post(route('admin.scrutins.store'), $this->validPayload());

        $response->assertForbidden();
        $this->assertDatabaseCount('scrutins', 0);
    }

    public function test_referent_cannot_create_scrutin(): void
    {
        $referent = User::factory()->referent()->create();

        $response = $this->actingAs($referent)->post(route('admin.scrutins.store'), $this->validPayload());

        $response->assertForbidden();
    }

    // ----------------------------------------------------------------
    // Scénario 3 — Publication draft → open
    // ----------------------------------------------------------------

    public function test_admin_can_publish_draft_scrutin(): void
    {
        $admin = User::factory()->admin()->create();
        $scrutin = Scrutin::factory()->draft()->create(['created_by' => $admin->id]);
        ScrutinOption::factory()->create(['scrutin_id' => $scrutin->id, 'position' => 1]);
        ScrutinOption::factory()->create(['scrutin_id' => $scrutin->id, 'position' => 2]);

        $response = $this->actingAs($admin)->post(route('admin.scrutins.publish', $scrutin));

        $response->assertRedirect();
        $this->assertDatabaseHas('scrutins', ['id' => $scrutin->id, 'status' => ScrutinStatus::Open->value]);
    }

    // ----------------------------------------------------------------
    // Scénario 4 — Publication bloquée si moins de 2 options
    // ----------------------------------------------------------------

    public function test_cannot_publish_scrutin_with_fewer_than_two_options(): void
    {
        $admin = User::factory()->admin()->create();
        $scrutin = Scrutin::factory()->draft()->create(['created_by' => $admin->id]);
        ScrutinOption::factory()->create(['scrutin_id' => $scrutin->id, 'position' => 1]);

        $response = $this->actingAs($admin)->post(route('admin.scrutins.publish', $scrutin));

        $response->assertRedirect();
        $this->assertDatabaseHas('scrutins', ['id' => $scrutin->id, 'status' => ScrutinStatus::Draft->value]);
    }

    // ----------------------------------------------------------------
    // Scénario 5 — closes_at doit être après opened_at
    // ----------------------------------------------------------------

    public function test_closes_at_must_be_after_opened_at(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.scrutins.store'), $this->validPayload([
            'opened_at' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'closes_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
        ]));

        $response->assertSessionHasErrors('closes_at');
        $this->assertDatabaseCount('scrutins', 0);
    }

    // ----------------------------------------------------------------
    // Scénario 6 — Membre vote une fois ; double vote bloqué
    // ----------------------------------------------------------------

    public function test_authenticated_user_can_vote_once(): void
    {
        $adherent = User::factory()->adherent()->create();
        [$scrutin, $optionA] = $this->openScrutinWithOptions();

        $response = $this->actingAs($adherent)->post(route('member.scrutins.vote', $scrutin), [
            'scrutin_option_id' => $optionA->id,
        ]);

        $response->assertRedirect(route('member.scrutins.show', $scrutin));
        $this->assertDatabaseHas('scrutin_votes', [
            'scrutin_id' => $scrutin->id,
            'scrutin_option_id' => $optionA->id,
            'user_id' => $adherent->id,
        ]);
        $this->assertDatabaseCount('scrutin_votes', 1);
    }

    public function test_member_cannot_vote_twice(): void
    {
        $adherent = User::factory()->adherent()->create();
        [$scrutin, $optionA, $optionB] = $this->openScrutinWithOptions();

        ScrutinVote::create([
            'scrutin_id' => $scrutin->id,
            'scrutin_option_id' => $optionA->id,
            'user_id' => $adherent->id,
        ]);

        $response = $this->actingAs($adherent)->post(route('member.scrutins.vote', $scrutin), [
            'scrutin_option_id' => $optionB->id,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('scrutin_votes', 1);
    }

    // ----------------------------------------------------------------
    // Scénario 7 — Vote bloqué sur scrutin draft/fermé
    // ----------------------------------------------------------------

    public function test_cannot_vote_on_draft_scrutin(): void
    {
        $adherent = User::factory()->adherent()->create();
        $scrutin = Scrutin::factory()->draft()->create();
        $option = ScrutinOption::factory()->create(['scrutin_id' => $scrutin->id]);

        $response = $this->actingAs($adherent)->post(route('member.scrutins.vote', $scrutin), [
            'scrutin_option_id' => $option->id,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('scrutin_votes', 0);
    }

    public function test_cannot_vote_on_closed_scrutin(): void
    {
        $adherent = User::factory()->adherent()->create();
        $scrutin = Scrutin::factory()->closed()->create();
        $option = ScrutinOption::factory()->create(['scrutin_id' => $scrutin->id]);

        $response = $this->actingAs($adherent)->post(route('member.scrutins.vote', $scrutin), [
            'scrutin_option_id' => $option->id,
        ]);

        $response->assertForbidden();
    }

    // ----------------------------------------------------------------
    // Scénario 8 — Commande auto-close + résultat adopted
    // ----------------------------------------------------------------

    public function test_artisan_command_closes_expired_scrutins_and_computes_results(): void
    {
        // 3 membres actifs
        $users = User::factory()->adherent()->count(3)->create();

        $scrutin = Scrutin::factory()->open()->create([
            'status' => ScrutinStatus::Open,
            'closes_at' => now()->subMinute(),
            'quorum_type' => ScrutinQuorumType::Fixed,
            'quorum_value' => 2,
            'majority_type' => ScrutinMajorityType::Simple,
        ]);

        $optionA = ScrutinOption::factory()->create(['scrutin_id' => $scrutin->id, 'position' => 1]);
        $optionB = ScrutinOption::factory()->create(['scrutin_id' => $scrutin->id, 'position' => 2]);

        ScrutinVote::create(['scrutin_id' => $scrutin->id, 'scrutin_option_id' => $optionA->id, 'user_id' => $users[0]->id]);
        ScrutinVote::create(['scrutin_id' => $scrutin->id, 'scrutin_option_id' => $optionA->id, 'user_id' => $users[1]->id]);
        ScrutinVote::create(['scrutin_id' => $scrutin->id, 'scrutin_option_id' => $optionB->id, 'user_id' => $users[2]->id]);

        Artisan::call('app:close-expired-scrutins');

        $this->assertDatabaseHas('scrutins', [
            'id' => $scrutin->id,
            'status' => ScrutinStatus::Closed->value,
            'result_status' => ScrutinResultStatus::Adopted->value,
            'total_votes' => 3,
            'winning_option_id' => $optionA->id,
        ]);
    }

    // ----------------------------------------------------------------
    // Scénario 9 — Quorum fixe non atteint
    // ----------------------------------------------------------------

    public function test_fixed_quorum_not_reached_marks_scrutin_null(): void
    {
        $service = app(ScrutinService::class);

        $users = User::factory()->adherent()->count(2)->create();

        $scrutin = Scrutin::factory()->open()->create([
            'status' => ScrutinStatus::Open,
            'closes_at' => now()->subMinute(),
            'quorum_type' => ScrutinQuorumType::Fixed,
            'quorum_value' => 10,
            'majority_type' => ScrutinMajorityType::Simple,
        ]);

        $optionA = ScrutinOption::factory()->create(['scrutin_id' => $scrutin->id, 'position' => 1]);
        ScrutinOption::factory()->create(['scrutin_id' => $scrutin->id, 'position' => 2]);

        ScrutinVote::create(['scrutin_id' => $scrutin->id, 'scrutin_option_id' => $optionA->id, 'user_id' => $users[0]->id]);
        ScrutinVote::create(['scrutin_id' => $scrutin->id, 'scrutin_option_id' => $optionA->id, 'user_id' => $users[1]->id]);

        $service->close($scrutin, null);

        $this->assertDatabaseHas('scrutins', [
            'id' => $scrutin->id,
            'status' => ScrutinStatus::Closed->value,
            'result_status' => ScrutinResultStatus::QuorumNotReached->value,
        ]);
    }

    // ----------------------------------------------------------------
    // Scénario 10 — Quorum proportionnel
    // ----------------------------------------------------------------

    public function test_proportional_quorum_not_reached_with_insufficient_votes(): void
    {
        $service = app(ScrutinService::class);

        // 10 membres actifs, quorum = 50% → ceil(10*50/100) = 5 requis
        $members = User::factory()->adherent()->count(10)->create();

        $scrutin = Scrutin::factory()->open()->create([
            'closes_at' => now()->subMinute(),
            'quorum_type' => ScrutinQuorumType::Proportional,
            'quorum_value' => 50,
            'majority_type' => ScrutinMajorityType::Simple,
        ]);

        $optionA = ScrutinOption::factory()->create(['scrutin_id' => $scrutin->id, 'position' => 1]);
        ScrutinOption::factory()->create(['scrutin_id' => $scrutin->id, 'position' => 2]);

        // Seulement 4 votes parmi les 10 membres (< 5 requis)
        foreach ($members->take(4) as $voter) {
            ScrutinVote::create(['scrutin_id' => $scrutin->id, 'scrutin_option_id' => $optionA->id, 'user_id' => $voter->id]);
        }

        $service->close($scrutin, null);

        $this->assertDatabaseHas('scrutins', [
            'id' => $scrutin->id,
            'result_status' => ScrutinResultStatus::QuorumNotReached->value,
        ]);
    }

    public function test_proportional_quorum_reached_with_sufficient_votes(): void
    {
        $service = app(ScrutinService::class);

        // 10 membres actifs, quorum = 50% → ceil(10*50/100) = 5 requis
        $voters = User::factory()->adherent()->count(10)->create();

        $scrutin = Scrutin::factory()->open()->create([
            'closes_at' => now()->subMinute(),
            'quorum_type' => ScrutinQuorumType::Proportional,
            'quorum_value' => 50,
            'majority_type' => ScrutinMajorityType::Simple,
        ]);

        $optionA = ScrutinOption::factory()->create(['scrutin_id' => $scrutin->id, 'position' => 1]);
        ScrutinOption::factory()->create(['scrutin_id' => $scrutin->id, 'position' => 2]);

        // 5 votes parmi les 10 membres (= seuil exact)
        foreach ($voters->take(5) as $voter) {
            ScrutinVote::create(['scrutin_id' => $scrutin->id, 'scrutin_option_id' => $optionA->id, 'user_id' => $voter->id]);
        }

        $service->close($scrutin, null);

        // 10 membres actifs, 5 votes : quorum atteint ; majorité simple (5 > 5/2=2.5) → adopted
        $this->assertDatabaseHas('scrutins', [
            'id' => $scrutin->id,
            'result_status' => ScrutinResultStatus::Adopted->value,
        ]);
    }

    // ----------------------------------------------------------------
    // Scénario 11 — Majorité qualifiée
    // ----------------------------------------------------------------

    public function test_qualified_majority_threshold_not_reached(): void
    {
        $service = app(ScrutinService::class);

        $scrutin = Scrutin::factory()->open()->create([
            'closes_at' => now()->subMinute(),
            'quorum_type' => ScrutinQuorumType::Fixed,
            'quorum_value' => 1,
            'majority_type' => ScrutinMajorityType::Qualified,
            'majority_threshold' => 66.67,
        ]);

        $optionA = ScrutinOption::factory()->create(['scrutin_id' => $scrutin->id, 'position' => 1]);
        $optionB = ScrutinOption::factory()->create(['scrutin_id' => $scrutin->id, 'position' => 2]);

        // 3 votes : A=2, B=1 — seuil = 3 * 66.67/100 = 2.0001 → 2 > 2.0001 is false
        $voters = User::factory()->adherent()->count(3)->create();
        ScrutinVote::create(['scrutin_id' => $scrutin->id, 'scrutin_option_id' => $optionA->id, 'user_id' => $voters[0]->id]);
        ScrutinVote::create(['scrutin_id' => $scrutin->id, 'scrutin_option_id' => $optionA->id, 'user_id' => $voters[1]->id]);
        ScrutinVote::create(['scrutin_id' => $scrutin->id, 'scrutin_option_id' => $optionB->id, 'user_id' => $voters[2]->id]);

        $service->close($scrutin, null);

        $this->assertDatabaseHas('scrutins', [
            'id' => $scrutin->id,
            'result_status' => ScrutinResultStatus::NoDecision->value,
        ]);
    }

    public function test_qualified_majority_threshold_reached(): void
    {
        $service = app(ScrutinService::class);

        $scrutin = Scrutin::factory()->open()->create([
            'closes_at' => now()->subMinute(),
            'quorum_type' => ScrutinQuorumType::Fixed,
            'quorum_value' => 1,
            'majority_type' => ScrutinMajorityType::Qualified,
            'majority_threshold' => 66.67,
        ]);

        $optionA = ScrutinOption::factory()->create(['scrutin_id' => $scrutin->id, 'position' => 1]);
        ScrutinOption::factory()->create(['scrutin_id' => $scrutin->id, 'position' => 2]);

        // 3 votes tous sur A → 3 > 3*66.67/100=2.0001 → adopted
        $voters = User::factory()->adherent()->count(3)->create();
        foreach ($voters as $voter) {
            ScrutinVote::create(['scrutin_id' => $scrutin->id, 'scrutin_option_id' => $optionA->id, 'user_id' => $voter->id]);
        }

        $service->close($scrutin, null);

        $this->assertDatabaseHas('scrutins', [
            'id' => $scrutin->id,
            'result_status' => ScrutinResultStatus::Adopted->value,
            'winning_option_id' => $optionA->id,
        ]);
    }

    // ----------------------------------------------------------------
    // Scénario 12 — Clôture manuelle par admin
    // ----------------------------------------------------------------

    public function test_admin_can_manually_close_open_scrutin(): void
    {
        $admin = User::factory()->admin()->create();
        [$scrutin, $optionA] = $this->openScrutinWithOptions();

        $response = $this->actingAs($admin)->post(route('admin.scrutins.close', $scrutin));

        $response->assertRedirect();
        $this->assertDatabaseHas('scrutins', [
            'id' => $scrutin->id,
            'status' => ScrutinStatus::Closed->value,
        ]);
    }

    // ----------------------------------------------------------------
    // Scénario 13 — Annulation
    // ----------------------------------------------------------------

    public function test_admin_can_cancel_draft_scrutin(): void
    {
        $admin = User::factory()->admin()->create();
        $scrutin = Scrutin::factory()->draft()->create(['created_by' => $admin->id]);

        $response = $this->actingAs($admin)->post(route('admin.scrutins.cancel', $scrutin));

        $response->assertRedirect();
        $this->assertDatabaseHas('scrutins', ['id' => $scrutin->id, 'status' => ScrutinStatus::Cancelled->value]);
    }

    public function test_admin_can_cancel_open_scrutin_with_no_votes(): void
    {
        $admin = User::factory()->admin()->create();
        [$scrutin] = $this->openScrutinWithOptions(['created_by' => $admin->id]);

        $response = $this->actingAs($admin)->post(route('admin.scrutins.cancel', $scrutin));

        $response->assertRedirect();
        $this->assertDatabaseHas('scrutins', ['id' => $scrutin->id, 'status' => ScrutinStatus::Cancelled->value]);
    }

    public function test_cannot_cancel_open_scrutin_with_votes(): void
    {
        $admin = User::factory()->admin()->create();
        $adherent = User::factory()->adherent()->create();
        [$scrutin, $optionA] = $this->openScrutinWithOptions();

        ScrutinVote::create([
            'scrutin_id' => $scrutin->id,
            'scrutin_option_id' => $optionA->id,
            'user_id' => $adherent->id,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.scrutins.cancel', $scrutin));

        $response->assertForbidden();
        $this->assertDatabaseHas('scrutins', ['id' => $scrutin->id, 'status' => ScrutinStatus::Open->value]);
    }

    // ----------------------------------------------------------------
    // Scénario 14 — Édition bloquée après publication
    // ----------------------------------------------------------------

    public function test_cannot_edit_open_scrutin(): void
    {
        $admin = User::factory()->admin()->create();
        [$scrutin] = $this->openScrutinWithOptions();

        $response = $this->actingAs($admin)->put(route('admin.scrutins.update', $scrutin), $this->validPayload());

        $response->assertForbidden();
    }

    // ----------------------------------------------------------------
    // Scénario 15 — Lazy close au chargement de la page membre
    // ----------------------------------------------------------------

    public function test_expired_scrutin_is_auto_closed_on_member_show(): void
    {
        $adherent = User::factory()->adherent()->create();
        $scrutin = Scrutin::factory()->open()->create([
            'closes_at' => now()->subMinute(),
        ]);
        ScrutinOption::factory()->create(['scrutin_id' => $scrutin->id, 'position' => 1]);
        ScrutinOption::factory()->create(['scrutin_id' => $scrutin->id, 'position' => 2]);

        $this->actingAs($adherent)->get(route('member.scrutins.show', $scrutin));

        $this->assertDatabaseHas('scrutins', [
            'id' => $scrutin->id,
            'status' => ScrutinStatus::Closed->value,
        ]);
    }

    // ----------------------------------------------------------------
    // Scénario 16 — Guest redirigé vers login
    // ----------------------------------------------------------------

    public function test_guest_is_redirected_to_login_for_member_scrutins(): void
    {
        $response = $this->get(route('member.scrutins.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_to_login_for_admin_scrutins(): void
    {
        $response = $this->get(route('admin.scrutins.index'));
        $response->assertRedirect(route('login'));
    }

    // ----------------------------------------------------------------
    // Scénario bonus — Options invalides refusées
    // ----------------------------------------------------------------

    public function test_cannot_store_scrutin_with_fewer_than_two_options(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.scrutins.store'), $this->validPayload([
            'options' => [
                ['label' => 'Seule option', 'position' => 1],
            ],
        ]));

        $response->assertSessionHasErrors('options');
        $this->assertDatabaseCount('scrutins', 0);
    }
}
