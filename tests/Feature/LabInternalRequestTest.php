<?php

namespace Tests\Feature;

use App\Enums\LabRequestStatus;
use App\Models\Circle;
use App\Models\LabInternalRequest;
use App\Models\LabService;
use App\Models\User;
use App\Notifications\LabInternalRequestReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LabInternalRequestTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Soumission d'une demande
    // -------------------------------------------------------

    public function test_any_auth_user_can_submit_a_request(): void
    {
        $user = User::factory()->create();
        $circle = Circle::factory()->create();

        $this->actingAs($user)
            ->post(route('lab.requests.store'), [
                'circle_id' => $circle->id,
                'message' => 'Nous aimerions un accompagnement sur notre projet.',
                'desired_date' => null,
            ])
            ->assertRedirect(route('lab.requests.my'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('lab_internal_requests', [
            'circle_id' => $circle->id,
            'user_id' => $user->id,
            'message' => 'Nous aimerions un accompagnement sur notre projet.',
            'status' => LabRequestStatus::Nouvelle->value,
        ]);
    }

    public function test_request_can_include_optional_service_and_date(): void
    {
        $user = User::factory()->create();
        $circle = Circle::factory()->create();
        $service = LabService::factory()->create();

        $this->actingAs($user)
            ->post(route('lab.requests.store'), [
                'circle_id' => $circle->id,
                'lab_service_id' => $service->id,
                'message' => 'Message avec service et date.',
                'desired_date' => now()->addMonth()->format('Y-m-d'),
            ])
            ->assertRedirect(route('lab.requests.my'));

        $this->assertDatabaseHas('lab_internal_requests', [
            'circle_id' => $circle->id,
            'lab_service_id' => $service->id,
        ]);
    }

    public function test_guest_is_redirected_when_submitting(): void
    {
        $circle = Circle::factory()->create();

        $this->post(route('lab.requests.store'), [
            'circle_id' => $circle->id,
            'message' => 'Un message.',
        ])->assertRedirect(route('login'));
    }

    public function test_message_is_required(): void
    {
        $user = User::factory()->create();
        $circle = Circle::factory()->create();

        $this->actingAs($user)
            ->post(route('lab.requests.store'), [
                'circle_id' => $circle->id,
                'message' => '',
            ])
            ->assertSessionHasErrors('message');
    }

    // -------------------------------------------------------
    // Notifications
    // -------------------------------------------------------

    public function test_notification_sent_to_lab_referents_and_admins_on_submit(): void
    {
        Notification::fake();

        $labReferent = User::factory()->referent()->create();
        Circle::factory()->create(['slug' => 'lab', 'referent_id' => $labReferent->id]);

        $admin = User::factory()->admin()->create();

        $user = User::factory()->create();
        $circle = Circle::factory()->create();

        $this->actingAs($user)
            ->post(route('lab.requests.store'), [
                'circle_id' => $circle->id,
                'message' => 'Besoin d\'aide pour faciliter un atelier.',
            ]);

        Notification::assertSentTo($labReferent, LabInternalRequestReceived::class);
        Notification::assertSentTo($admin, LabInternalRequestReceived::class);
    }

    public function test_notification_not_sent_to_non_lab_referent(): void
    {
        Notification::fake();

        $otherReferent = User::factory()->referent()->create();
        Circle::factory()->create(['slug' => 'sport', 'referent_id' => $otherReferent->id]);

        $user = User::factory()->create();
        $circle = Circle::factory()->create();

        $this->actingAs($user)
            ->post(route('lab.requests.store'), [
                'circle_id' => $circle->id,
                'message' => 'Message de test.',
            ]);

        Notification::assertNotSentTo($otherReferent, LabInternalRequestReceived::class);
    }

    // -------------------------------------------------------
    // Accès liste référents Lab (/lab/demandes)
    // -------------------------------------------------------

    public function test_lab_referent_can_access_requests_list(): void
    {
        $labReferent = User::factory()->referent()->create();
        Circle::factory()->create(['slug' => 'lab', 'referent_id' => $labReferent->id]);

        $this->actingAs($labReferent)
            ->get(route('lab.requests.index'))
            ->assertOk();
    }

    public function test_admin_can_access_requests_list(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('lab.requests.index'))
            ->assertOk();
    }

    public function test_non_lab_adherent_gets_403_on_requests_list(): void
    {
        $adherent = User::factory()->adherent()->create();

        $this->actingAs($adherent)
            ->get(route('lab.requests.index'))
            ->assertForbidden();
    }

    public function test_other_circle_referent_gets_403_on_requests_list(): void
    {
        $referent = User::factory()->referent()->create();
        Circle::factory()->create(['slug' => 'environnement', 'referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->get(route('lab.requests.index'))
            ->assertForbidden();
    }

    public function test_guest_gets_redirected_from_requests_list(): void
    {
        $this->get(route('lab.requests.index'))
            ->assertRedirect(route('login'));
    }

    // -------------------------------------------------------
    // Mise à jour du statut
    // -------------------------------------------------------

    public function test_lab_referent_can_update_status(): void
    {
        $labReferent = User::factory()->referent()->create();
        Circle::factory()->create(['slug' => 'lab', 'referent_id' => $labReferent->id]);

        $requester = User::factory()->create();
        $circle = Circle::factory()->create();
        $labRequest = LabInternalRequest::factory()->create([
            'user_id' => $requester->id,
            'circle_id' => $circle->id,
            'status' => LabRequestStatus::Nouvelle,
        ]);

        $this->actingAs($labReferent)
            ->patch(route('lab.requests.update-status', $labRequest), [
                'status' => LabRequestStatus::EnCours->value,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('lab_internal_requests', [
            'id' => $labRequest->id,
            'status' => LabRequestStatus::EnCours->value,
        ]);
    }

    public function test_adherent_cannot_update_status(): void
    {
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create();
        $labRequest = LabInternalRequest::factory()->create(['circle_id' => $circle->id]);

        $this->actingAs($adherent)
            ->patch(route('lab.requests.update-status', $labRequest), [
                'status' => LabRequestStatus::EnCours->value,
            ])
            ->assertForbidden();
    }

    // -------------------------------------------------------
    // Mes demandes (/lab/mes-demandes)
    // -------------------------------------------------------

    public function test_auth_user_can_view_own_requests(): void
    {
        $user = User::factory()->create();
        $circle = Circle::factory()->create();
        LabInternalRequest::factory()->create([
            'user_id' => $user->id,
            'circle_id' => $circle->id,
            'message' => 'Mon message unique.',
        ]);

        $this->actingAs($user)
            ->get(route('lab.requests.my'))
            ->assertOk()
            ->assertSee('Mon message unique.');
    }

    public function test_auth_user_cannot_see_others_requests_in_my_view(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $circle = Circle::factory()->create();

        LabInternalRequest::factory()->create([
            'user_id' => $userB->id,
            'circle_id' => $circle->id,
            'message' => 'Message de userB.',
        ]);

        $this->actingAs($userA)
            ->get(route('lab.requests.my'))
            ->assertOk()
            ->assertDontSee('Message de userB.');
    }
}
