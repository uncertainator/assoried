<?php

namespace Tests\Feature\Admin;

use App\Enums\AccountStatus;
use App\Models\AuditLog;
use App\Models\Circle;
use App\Models\PollVote;
use App\Models\User;
use App\Notifications\CircleLeaveNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class MemberExclusionTest extends TestCase
{
    use RefreshDatabase;

    // ----------------------------------------------------------------
    // Authorization
    // ----------------------------------------------------------------
    public function test_admin_can_exclude_a_member(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $member = User::factory()->adherent()->create();

        $this->actingAs($admin)
            ->post(route('admin.members.exclude', $member), ['reason' => 'Comportement'])
            ->assertRedirect(route('admin.members.index'));

        $this->assertSame(AccountStatus::Excluded, $member->fresh()->account_status);
    }

    public function test_referent_cannot_exclude_a_member(): void
    {
        $referent = User::factory()->referent()->create();
        $member = User::factory()->adherent()->create();

        $this->actingAs($referent)
            ->post(route('admin.members.exclude', $member))
            ->assertStatus(403);

        $this->assertSame(AccountStatus::Active, $member->fresh()->account_status);
    }

    public function test_adherent_cannot_exclude_a_member(): void
    {
        $adherent = User::factory()->adherent()->create();
        $member = User::factory()->adherent()->create();

        $this->actingAs($adherent)
            ->post(route('admin.members.exclude', $member))
            ->assertStatus(403);

        $this->assertSame(AccountStatus::Active, $member->fresh()->account_status);
    }

    // ----------------------------------------------------------------
    // Nominal exclusion
    // ----------------------------------------------------------------
    public function test_exclusion_detaches_circles_anonymizes_and_records_audit(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $referentA = User::factory()->referent()->create();
        $referentB = User::factory()->referent()->create();
        $circleA = Circle::factory()->create(['referent_id' => $referentA->id]);
        $circleB = Circle::factory()->create(['referent_id' => $referentB->id]);

        $member = User::factory()->adherent()->create([
            'name' => 'Jean Dupont',
            'email' => 'jean@example.test',
        ]);
        $member->circles()->attach([$circleA->id, $circleB->id], ['joined_at' => now()]);

        PollVote::factory()->create(['user_id' => $member->id]);

        $this->actingAs($admin)
            ->post(route('admin.members.exclude', $member), ['reason' => 'Manquement aux statuts'])
            ->assertRedirect(route('admin.members.index'));

        // Status + circle detachment
        $this->assertSame(AccountStatus::Excluded, $member->fresh()->account_status);
        $this->assertDatabaseMissing('circle_user', ['user_id' => $member->id]);

        // PII anonymized
        $fresh = $member->fresh();
        $this->assertSame('Membre exclu', $fresh->name);
        $this->assertNotSame('jean@example.test', $fresh->email);
        $this->assertNull($fresh->password);
        $this->assertDatabaseMissing('users', ['email' => 'jean@example.test']);

        // Audit trace survives anonymization and keeps the pseudonymized link
        $log = AuditLog::where('type', AuditLog::TYPE_MEMBER_EXCLUSION)->first();
        $this->assertNotNull($log);
        $this->assertSame($admin->id, $log->actor_id);
        $this->assertSame($member->id, $log->target_user_id);
        $this->assertSame('Manquement aux statuts', $log->meta['reason']);

        // Referents notified via the existing member-leave channel
        Notification::assertSentTo($referentA, CircleLeaveNotification::class);
        Notification::assertSentTo($referentB, CircleLeaveNotification::class);
    }

    public function test_past_votes_survive_exclusion(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $member = User::factory()->adherent()->create();
        $vote = PollVote::factory()->create(['user_id' => $member->id]);

        $this->actingAs($admin)->post(route('admin.members.exclude', $member));

        $this->assertDatabaseHas('poll_votes', [
            'id' => $vote->id,
            'user_id' => $member->id,
        ]);
    }

    // ----------------------------------------------------------------
    // Idempotence
    // ----------------------------------------------------------------
    public function test_already_excluded_member_cannot_be_excluded_again(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $member = User::factory()->adherent()->create();

        $this->actingAs($admin)->post(route('admin.members.exclude', $member));
        $this->assertSame(1, AuditLog::where('type', AuditLog::TYPE_MEMBER_EXCLUSION)->count());

        // Second attempt is denied by the policy (already excluded).
        $this->actingAs($admin)
            ->post(route('admin.members.exclude', $member))
            ->assertStatus(403);

        $this->assertSame(1, AuditLog::where('type', AuditLog::TYPE_MEMBER_EXCLUSION)->count());
    }

    // ----------------------------------------------------------------
    // Login blocked after exclusion
    // ----------------------------------------------------------------
    public function test_excluded_member_cannot_login_with_password(): void
    {
        $member = User::factory()->excluded()->create();

        $this->post(route('login.password'), [
            'email' => $member->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email', null, 'password');

        $this->assertGuest();
    }

    public function test_excluded_member_cannot_login_with_magic_link(): void
    {
        $member = User::factory()->excluded()->create();

        $url = URL::temporarySignedRoute('auth.magic.verify', now()->addMinutes(15), [
            'email' => $member->email,
        ]);

        $this->get($url)->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
