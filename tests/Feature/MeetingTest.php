<?php

namespace Tests\Feature;

use App\Models\Circle;
use App\Models\CircleMembership;
use App\Models\Meeting;
use App\Models\User;
use App\Notifications\MeetingCreatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MeetingTest extends TestCase
{
    use RefreshDatabase;

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    private function circleWithReferent(): array
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        return [$circle, $referent];
    }

    private function approvedMember(Circle $circle): User
    {
        $member = User::factory()->adherent()->create();
        CircleMembership::factory()->approved()->create([
            'user_id' => $member->id,
            'circle_id' => $circle->id,
        ]);

        return $member;
    }

    private function validPayload(): array
    {
        return [
            'title' => 'Réunion de test',
            'scheduled_at' => now()->addDays(7)->format('Y-m-d\TH:i'),
            'duration_minutes' => 60,
            'location' => 'Salle commune',
            'visio_url' => null,
            'agenda_items' => [
                ['title' => 'Point 1', 'duration_minutes' => 15],
                ['title' => 'Point 2', 'duration_minutes' => null],
            ],
        ];
    }

    // ----------------------------------------------------------------
    // 1. Accès à la liste
    // ----------------------------------------------------------------

    public function test_referent_can_view_meetings_list_of_their_circle(): void
    {
        [$circle, $referent] = $this->circleWithReferent();

        $response = $this->actingAs($referent)->get(route('member.circles.meetings.index', $circle));

        $response->assertOk();
    }

    public function test_approved_member_can_view_meetings_list(): void
    {
        [$circle] = $this->circleWithReferent();
        $member = $this->approvedMember($circle);

        $response = $this->actingAs($member)->get(route('member.circles.meetings.index', $circle));

        $response->assertOk();
    }

    public function test_non_member_cannot_view_meetings_list(): void
    {
        [$circle] = $this->circleWithReferent();
        $outsider = User::factory()->adherent()->create();

        $response = $this->actingAs($outsider)->get(route('member.circles.meetings.index', $circle));

        $response->assertForbidden();
    }

    public function test_admin_can_view_meetings_list_of_any_circle(): void
    {
        [$circle] = $this->circleWithReferent();
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('member.circles.meetings.index', $circle));

        $response->assertOk();
    }

    // ----------------------------------------------------------------
    // 2. Formulaire de création
    // ----------------------------------------------------------------

    public function test_referent_can_access_meeting_creation_form(): void
    {
        [$circle, $referent] = $this->circleWithReferent();

        $response = $this->actingAs($referent)->get(route('member.meetings.create', $circle));

        $response->assertOk();
    }

    public function test_adherent_cannot_access_meeting_creation_form(): void
    {
        [$circle] = $this->circleWithReferent();
        $member = $this->approvedMember($circle);

        $response = $this->actingAs($member)->get(route('member.meetings.create', $circle));

        $response->assertForbidden();
    }

    // ----------------------------------------------------------------
    // 3. Création valide
    // ----------------------------------------------------------------

    public function test_referent_can_create_a_valid_meeting(): void
    {
        Notification::fake();

        [$circle, $referent] = $this->circleWithReferent();

        $response = $this->actingAs($referent)
            ->post(route('member.meetings.store', $circle), $this->validPayload());

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $meeting = Meeting::first();
        $this->assertNotNull($meeting);
        $this->assertEquals('Réunion de test', $meeting->title);
        $this->assertEquals($circle->id, $meeting->circle_id);
        $this->assertEquals($referent->id, $meeting->created_by);

        $this->assertDatabaseCount('meeting_agenda_items', 2);
        $this->assertDatabaseHas('meeting_agenda_items', ['position' => 1, 'title' => 'Point 1', 'duration_minutes' => 15]);
        $this->assertDatabaseHas('meeting_agenda_items', ['position' => 2, 'title' => 'Point 2', 'duration_minutes' => null]);

        $response->assertRedirect(route('member.meetings.show', $meeting));
    }

    public function test_admin_can_create_meeting_for_any_circle(): void
    {
        Notification::fake();

        [$circle] = $this->circleWithReferent();
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->post(route('member.meetings.store', $circle), $this->validPayload());

        $response->assertRedirect();
        $this->assertDatabaseCount('meetings', 1);
    }

    // ----------------------------------------------------------------
    // 4. Contrôle d'autorisation inter-cercles
    // ----------------------------------------------------------------

    public function test_referent_of_circle_a_cannot_create_meeting_for_circle_b(): void
    {
        Notification::fake();

        [$circleA, $referentA] = $this->circleWithReferent();
        [$circleB] = $this->circleWithReferent();

        $response = $this->actingAs($referentA)
            ->post(route('member.meetings.store', $circleB), $this->validPayload());

        $response->assertForbidden();
        $this->assertDatabaseCount('meetings', 0);
    }

    // ----------------------------------------------------------------
    // 5. Validation du formulaire
    // ----------------------------------------------------------------

    public function test_creation_fails_without_title(): void
    {
        [$circle, $referent] = $this->circleWithReferent();
        $payload = array_merge($this->validPayload(), ['title' => '']);

        $response = $this->actingAs($referent)
            ->post(route('member.meetings.store', $circle), $payload);

        $response->assertSessionHasErrors('title');
        $this->assertDatabaseCount('meetings', 0);
    }

    public function test_creation_fails_with_past_date(): void
    {
        [$circle, $referent] = $this->circleWithReferent();
        $payload = array_merge($this->validPayload(), ['scheduled_at' => now()->subDay()->format('Y-m-d\TH:i')]);

        $response = $this->actingAs($referent)
            ->post(route('member.meetings.store', $circle), $payload);

        $response->assertSessionHasErrors('scheduled_at');
        $this->assertDatabaseCount('meetings', 0);
    }

    public function test_creation_fails_without_agenda_items(): void
    {
        [$circle, $referent] = $this->circleWithReferent();
        $payload = array_merge($this->validPayload(), ['agenda_items' => []]);

        $response = $this->actingAs($referent)
            ->post(route('member.meetings.store', $circle), $payload);

        $response->assertSessionHasErrors('agenda_items');
        $this->assertDatabaseCount('meetings', 0);
    }

    public function test_creation_fails_if_agenda_item_title_is_missing(): void
    {
        [$circle, $referent] = $this->circleWithReferent();
        $payload = array_merge($this->validPayload(), [
            'agenda_items' => [['title' => '', 'duration_minutes' => null]],
        ]);

        $response = $this->actingAs($referent)
            ->post(route('member.meetings.store', $circle), $payload);

        $response->assertSessionHasErrors('agenda_items.0.title');
        $this->assertDatabaseCount('meetings', 0);
    }

    public function test_creation_fails_if_visio_url_is_not_valid(): void
    {
        [$circle, $referent] = $this->circleWithReferent();
        $payload = array_merge($this->validPayload(), ['visio_url' => 'not-a-url']);

        $response = $this->actingAs($referent)
            ->post(route('member.meetings.store', $circle), $payload);

        $response->assertSessionHasErrors('visio_url');
        $this->assertDatabaseCount('meetings', 0);
    }

    // ----------------------------------------------------------------
    // 6. Notifications
    // ----------------------------------------------------------------

    public function test_notification_is_sent_to_approved_members_on_creation(): void
    {
        Notification::fake();

        [$circle, $referent] = $this->circleWithReferent();
        $approvedMember = $this->approvedMember($circle);

        $pendingMember = User::factory()->adherent()->create();
        CircleMembership::factory()->pending()->create([
            'user_id' => $pendingMember->id,
            'circle_id' => $circle->id,
        ]);

        $this->actingAs($referent)
            ->post(route('member.meetings.store', $circle), $this->validPayload());

        Notification::assertSentTo($approvedMember, MeetingCreatedNotification::class);
        Notification::assertNotSentTo($pendingMember, MeetingCreatedNotification::class);
    }

    // ----------------------------------------------------------------
    // 7. Page de détail
    // ----------------------------------------------------------------

    public function test_approved_member_can_view_meeting_detail(): void
    {
        [$circle] = $this->circleWithReferent();
        $member = $this->approvedMember($circle);
        $meeting = Meeting::factory()->create(['circle_id' => $circle->id]);

        $response = $this->actingAs($member)->get(route('member.meetings.show', $meeting));

        $response->assertOk();
    }

    public function test_non_member_cannot_view_meeting_detail(): void
    {
        [$circle] = $this->circleWithReferent();
        $meeting = Meeting::factory()->create(['circle_id' => $circle->id]);
        $outsider = User::factory()->adherent()->create();

        $response = $this->actingAs($outsider)->get(route('member.meetings.show', $meeting));

        $response->assertForbidden();
    }
}
