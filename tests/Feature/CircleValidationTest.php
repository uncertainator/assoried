<?php

namespace Tests\Feature;

use App\Enums\MembershipStatus;
use App\Enums\UserRole;
use App\Models\Circle;
use App\Models\CircleMembership;
use App\Models\User;
use App\Notifications\CircleJoinDecisionNotification;
use App\Notifications\CircleJoinRequestNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CircleValidationTest extends TestCase
{
    use RefreshDatabase;

    // ----------------------------------------------------------------
    // 1. Demande réussie
    // ----------------------------------------------------------------
    public function test_adherent_can_submit_join_request(): void
    {
        Notification::fake();

        $referent = User::factory()->referent()->create();
        $circle   = Circle::factory()->create(['referent_id' => $referent->id]);
        $adherent = User::factory()->adherent()->create();

        $response = $this->actingAs($adherent)->post(route('member.circles.join', $circle));

        $response->assertRedirect();
        $this->assertDatabaseHas('circle_user', [
            'user_id'   => $adherent->id,
            'circle_id' => $circle->id,
            'status'    => 'pending',
        ]);
        Notification::assertSentTo($referent, CircleJoinRequestNotification::class);
    }

    // ----------------------------------------------------------------
    // 2. Doublon pending
    // ----------------------------------------------------------------
    public function test_duplicate_pending_request_is_rejected(): void
    {
        $circle   = Circle::factory()->create();
        $adherent = User::factory()->adherent()->create();

        CircleMembership::factory()->pending()->create([
            'user_id'   => $adherent->id,
            'circle_id' => $circle->id,
        ]);

        $response = $this->actingAs($adherent)->post(route('member.circles.join', $circle));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('circle_user', 1);
    }

    // ----------------------------------------------------------------
    // 3. Déjà approved
    // ----------------------------------------------------------------
    public function test_already_approved_member_cannot_rejoin(): void
    {
        $circle   = Circle::factory()->create();
        $adherent = User::factory()->adherent()->create();

        CircleMembership::factory()->approved()->create([
            'user_id'   => $adherent->id,
            'circle_id' => $circle->id,
        ]);

        $response = $this->actingAs($adherent)->post(route('member.circles.join', $circle));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // ----------------------------------------------------------------
    // 4. Re-candidature après refus
    // ----------------------------------------------------------------
    public function test_rejected_member_can_reapply(): void
    {
        Notification::fake();

        $circle   = Circle::factory()->create();
        $adherent = User::factory()->adherent()->create();

        CircleMembership::factory()->rejected()->create([
            'user_id'   => $adherent->id,
            'circle_id' => $circle->id,
        ]);

        $this->actingAs($adherent)->post(route('member.circles.join', $circle));

        $this->assertDatabaseHas('circle_user', [
            'user_id'   => $adherent->id,
            'circle_id' => $circle->id,
            'status'    => 'pending',
        ]);
        $this->assertDatabaseCount('circle_user', 1);
    }

    // ----------------------------------------------------------------
    // 5. Annulation pending
    // ----------------------------------------------------------------
    public function test_adherent_can_cancel_pending_request(): void
    {
        Notification::fake();

        $circle   = Circle::factory()->create();
        $adherent = User::factory()->adherent()->create();

        CircleMembership::factory()->pending()->create([
            'user_id'   => $adherent->id,
            'circle_id' => $circle->id,
        ]);

        $response = $this->actingAs($adherent)->delete(route('member.circles.cancel', $circle));

        $response->assertRedirect();
        $this->assertDatabaseMissing('circle_user', [
            'user_id'   => $adherent->id,
            'circle_id' => $circle->id,
        ]);
        Notification::assertNothingSent();
    }

    // ----------------------------------------------------------------
    // 6. Annulation refusée si approved
    // ----------------------------------------------------------------
    public function test_adherent_cannot_cancel_approved_membership(): void
    {
        $circle   = Circle::factory()->create();
        $adherent = User::factory()->adherent()->create();

        CircleMembership::factory()->approved()->create([
            'user_id'   => $adherent->id,
            'circle_id' => $circle->id,
        ]);

        $response = $this->actingAs($adherent)->delete(route('member.circles.cancel', $circle));

        $response->assertStatus(403);
    }

    // ----------------------------------------------------------------
    // 7. Approve par référent
    // ----------------------------------------------------------------
    public function test_referent_can_approve_membership(): void
    {
        Notification::fake();

        $referent = User::factory()->referent()->create();
        $circle   = Circle::factory()->create(['referent_id' => $referent->id]);
        $adherent = User::factory()->adherent()->create();

        $membership = CircleMembership::factory()->pending()->create([
            'user_id'   => $adherent->id,
            'circle_id' => $circle->id,
        ]);

        $response = $this->actingAs($referent)
            ->post(route('referent.requests.approve', $membership));

        $response->assertRedirect();
        $this->assertDatabaseHas('circle_user', [
            'id'           => $membership->id,
            'status'       => 'approved',
            'validated_by' => $referent->id,
        ]);
        Notification::assertSentTo($adherent, CircleJoinDecisionNotification::class);
    }

    // ----------------------------------------------------------------
    // 8. Reject avec motif
    // ----------------------------------------------------------------
    public function test_referent_can_reject_with_reason(): void
    {
        Notification::fake();

        $referent = User::factory()->referent()->create();
        $circle   = Circle::factory()->create(['referent_id' => $referent->id]);
        $adherent = User::factory()->adherent()->create();

        $membership = CircleMembership::factory()->pending()->create([
            'user_id'   => $adherent->id,
            'circle_id' => $circle->id,
        ]);

        $response = $this->actingAs($referent)
            ->post(route('referent.requests.reject', $membership), [
                'reason' => 'Le cercle est complet pour cette session.',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('circle_user', [
            'id'               => $membership->id,
            'status'           => 'rejected',
            'rejection_reason' => 'Le cercle est complet pour cette session.',
        ]);
        Notification::assertSentTo($adherent, CircleJoinDecisionNotification::class);
    }

    // ----------------------------------------------------------------
    // 9. 403 référent mauvais cercle
    // ----------------------------------------------------------------
    public function test_referent_cannot_approve_request_for_another_circle(): void
    {
        $referent      = User::factory()->referent()->create();
        $circleA       = Circle::factory()->create(['referent_id' => $referent->id]);
        $circleB       = Circle::factory()->create();
        $adherent      = User::factory()->adherent()->create();

        $membership = CircleMembership::factory()->pending()->create([
            'user_id'   => $adherent->id,
            'circle_id' => $circleB->id,
        ]);

        $response = $this->actingAs($referent)
            ->post(route('referent.requests.approve', $membership));

        $response->assertStatus(403);
    }

    // ----------------------------------------------------------------
    // 10. Cercle sans référent → admins only
    // ----------------------------------------------------------------
    public function test_notification_sent_to_admins_only_when_no_referent(): void
    {
        Notification::fake();

        $admin    = User::factory()->admin()->create();
        $circle   = Circle::factory()->create(['referent_id' => null]);
        $adherent = User::factory()->adherent()->create();

        $this->actingAs($adherent)->post(route('member.circles.join', $circle));

        Notification::assertSentTo($admin, CircleJoinRequestNotification::class);
        Notification::assertNotSentTo($adherent, CircleJoinRequestNotification::class);
    }

    // ----------------------------------------------------------------
    // 11. Badge count référent
    // ----------------------------------------------------------------
    public function test_nav_badge_shows_pending_count_for_referent(): void
    {
        $referent = User::factory()->referent()->create();
        $circle   = Circle::factory()->create(['referent_id' => $referent->id]);

        CircleMembership::factory()->pending()->count(3)->create(['circle_id' => $circle->id]);

        $response = $this->actingAs($referent)->get(route('referent.requests.index'));

        $response->assertSee('3');
    }

    // ----------------------------------------------------------------
    // 12. Marquer notification lue
    // ----------------------------------------------------------------
    public function test_adherent_can_mark_notification_as_read(): void
    {
        $adherent = User::factory()->adherent()->create();
        $circle   = Circle::factory()->create();

        $adherent->notifications()->create([
            'id'              => \Illuminate\Support\Str::uuid(),
            'type'            => CircleJoinDecisionNotification::class,
            'data'            => json_encode(['circle_name' => $circle->name, 'decision' => 'approved']),
            'read_at'         => null,
        ]);

        $notification = $adherent->unreadNotifications()->first();

        $response = $this->actingAs($adherent)
            ->post(route('member.notifications.read', $notification->id));

        $response->assertRedirect();
        $this->assertNotNull($notification->fresh()->read_at);
    }

    // ----------------------------------------------------------------
    // 13. Rétrogradation référent → demandes visibles dans admin
    // ----------------------------------------------------------------
    public function test_admin_sees_requests_after_referent_demotion(): void
    {
        $admin    = User::factory()->admin()->create();
        $circle   = Circle::factory()->create(['referent_id' => null]);
        $adherent = User::factory()->adherent()->create();

        CircleMembership::factory()->pending()->create([
            'user_id'   => $adherent->id,
            'circle_id' => $circle->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.requests.index'));

        $response->assertOk();
        $response->assertSee($adherent->email);
    }

    // ----------------------------------------------------------------
    // 14. Admin peut approuver n'importe quel cercle
    // ----------------------------------------------------------------
    public function test_admin_can_approve_any_circle_request(): void
    {
        Notification::fake();

        $admin    = User::factory()->admin()->create();
        $circle   = Circle::factory()->create();
        $adherent = User::factory()->adherent()->create();

        $membership = CircleMembership::factory()->pending()->create([
            'user_id'   => $adherent->id,
            'circle_id' => $circle->id,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.requests.approve', $membership));

        $response->assertRedirect();
        $this->assertDatabaseHas('circle_user', [
            'id'     => $membership->id,
            'status' => 'approved',
        ]);
    }
}
