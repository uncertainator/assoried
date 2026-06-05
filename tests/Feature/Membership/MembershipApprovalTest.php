<?php

namespace Tests\Feature\Membership;

use App\Enums\AccountStatus;
use App\Mail\MembershipApprovedMail;
use App\Mail\MembershipRejectedMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MembershipApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_account_cannot_login_with_password(): void
    {
        $user = User::factory()->pending()->create(['password' => bcrypt('secret123')]);

        $response = $this->post(route('login.password'), [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors('email', null, 'password');
        $this->assertGuest();

        $message = $response->baseResponse->getSession()
            ->get('errors')->getBag('password')->first('email');
        $this->assertSame('Votre adhésion est en cours de validation par le bureau.', $message);
    }

    public function test_rejected_account_cannot_login_with_password(): void
    {
        $user = User::factory()->rejected()->create(['password' => bcrypt('secret123')]);

        $response = $this->post(route('login.password'), [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors('email', null, 'password');
        $this->assertGuest();
    }

    public function test_pending_account_cannot_reach_member_routes(): void
    {
        $user = User::factory()->pending()->create();

        $this->actingAs($user)
            ->get(route('member.dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_admin_list_shows_only_pending_accounts(): void
    {
        $admin = User::factory()->admin()->create();
        $pending = User::factory()->pending()->create();
        User::factory()->create(); // active, must not appear

        $this->actingAs($admin)
            ->get(route('admin.memberships.index'))
            ->assertOk()
            ->assertSee($pending->email);
    }

    public function test_non_admin_cannot_access_membership_admin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.memberships.index'))
            ->assertStatus(403);
    }

    public function test_admin_can_approve_pending_account(): void
    {
        Mail::fake();

        $admin = User::factory()->admin()->create();
        $pending = User::factory()->pending()->create();

        $this->actingAs($admin)
            ->post(route('admin.memberships.approve', $pending))
            ->assertRedirect(route('admin.memberships.index'));

        $this->assertSame(AccountStatus::Active, $pending->fresh()->account_status);
        Mail::assertSent(MembershipApprovedMail::class);

        // Disparaît de la liste des demandes en attente.
        $this->actingAs($admin)
            ->get(route('admin.memberships.index'))
            ->assertDontSee($pending->email);
    }

    public function test_approved_account_can_then_login(): void
    {
        $admin = User::factory()->admin()->create();
        $pending = User::factory()->pending()->create(['password' => bcrypt('secret123')]);

        $this->actingAs($admin)->post(route('admin.memberships.approve', $pending));
        $this->post(route('logout'));

        $response = $this->post(route('login.password'), [
            'email' => $pending->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('member.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_admin_can_reject_with_reason(): void
    {
        Mail::fake();

        $admin = User::factory()->admin()->create();
        $pending = User::factory()->pending()->create();

        $this->actingAs($admin)
            ->post(route('admin.memberships.reject', $pending), ['reason' => 'Hors zone géographique'])
            ->assertRedirect(route('admin.memberships.index'));

        $this->assertSame(AccountStatus::Rejected, $pending->fresh()->account_status);

        Mail::assertSent(MembershipRejectedMail::class, function (MembershipRejectedMail $mail) {
            return $mail->reason === 'Hors zone géographique';
        });
    }

    public function test_already_processed_account_cannot_be_approved_again(): void
    {
        $admin = User::factory()->admin()->create();
        $active = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.memberships.approve', $active))
            ->assertSessionHasErrors('account_status');

        $this->assertSame(AccountStatus::Active, $active->fresh()->account_status);
    }
}
