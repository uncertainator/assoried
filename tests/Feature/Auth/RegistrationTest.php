<?php

namespace Tests\Feature\Auth;

use App\Enums\AccountStatus;
use App\Mail\MagicLinkMail;
use App\Mail\MembershipPendingMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_user_registers_as_pending_without_login(): void
    {
        Mail::fake();

        $response = $this->post(route('inscription.store'), [
            'email' => 'nouveau@example.com',
            'auth_method' => 'password',
            'password' => 'motdepasse123',
            'password_confirmation' => 'motdepasse123',
        ]);

        $response->assertRedirect(route('auth.membership-pending'));
        $this->assertGuest();

        $user = User::where('email', 'nouveau@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->password);
        $this->assertSame(AccountStatus::Pending, $user->account_status);

        Mail::assertSent(MembershipPendingMail::class);
    }

    public function test_existing_magic_link_user_gets_password_set(): void
    {
        $existing = User::factory()->magicLinkOnly()->create(['email' => 'magic@example.com']);

        $response = $this->post(route('inscription.store'), [
            'email' => 'magic@example.com',
            'auth_method' => 'password',
            'password' => 'nouveaumdp123',
            'password_confirmation' => 'nouveaumdp123',
        ]);

        $response->assertRedirect(route('member.dashboard'));
        $this->assertAuthenticatedAs($existing);
        $this->assertNotNull($existing->fresh()->password);
    }

    public function test_existing_user_with_password_is_redirected_to_login(): void
    {
        User::factory()->create(['email' => 'existing@example.com', 'password' => bcrypt('secret')]);

        $response = $this->post(route('inscription.store'), [
            'email' => 'existing@example.com',
            'auth_method' => 'password',
            'password' => 'autrechose123',
            'password_confirmation' => 'autrechose123',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_magic_link_registration_sends_email(): void
    {
        Mail::fake();

        $this->post(route('inscription.store'), [
            'email' => 'test@example.com',
            'auth_method' => 'magic_link',
        ])->assertRedirect(route('auth.link-sent'));

        Mail::assertSent(MagicLinkMail::class);
    }

    public function test_password_can_be_used_to_login_once_account_is_activated(): void
    {
        $this->post(route('inscription.store'), [
            'email' => 'user@example.com',
            'auth_method' => 'password',
            'password' => 'monpassword1',
            'password_confirmation' => 'monpassword1',
        ]);

        // Tant que l'adhésion n'est pas validée, la connexion est bloquée.
        $blocked = $this->post(route('login.password'), [
            'email' => 'user@example.com',
            'password' => 'monpassword1',
        ]);
        $blocked->assertSessionHasErrors('email', null, 'password');
        $this->assertGuest();

        // Validation par le bureau.
        User::where('email', 'user@example.com')->update(['account_status' => AccountStatus::Active]);

        $response = $this->post(route('login.password'), [
            'email' => 'user@example.com',
            'password' => 'monpassword1',
        ]);

        $response->assertRedirect(route('member.dashboard'));
        $this->assertAuthenticated();
    }
}
