<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_always_returns_same_message_regardless_of_account_existence(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => 'noreply@does-not-exist.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Si un compte existe pour cet email, un lien a été envoyé.');
    }

    public function test_sends_reset_link_to_existing_account(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $response = $this->post(route('password.email'), [
            'email' => $user->email,
        ]);

        $response->assertSessionHas('status', 'Si un compte existe pour cet email, un lien a été envoyé.');
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_sends_reset_link_to_magic_link_only_account(): void
    {
        Notification::fake();
        $user = User::factory()->magicLinkOnly()->create();

        $this->post(route('password.email'), ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_shows_reset_form_for_valid_token(): void
    {
        $response = $this->get(route('password.reset', ['token' => 'sometoken']));

        $response->assertOk();
        $response->assertSee('reinitialisation-mot-de-passe', false);
    }

    public function test_resets_password_and_logs_in_automatically(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email]);

        $token = null;
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use (&$token) {
            $token = $notification->token;

            return true;
        });

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('member.dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->password);
    }

    public function test_allows_magic_link_user_to_create_password_via_reset_flow(): void
    {
        Notification::fake();
        $user = User::factory()->magicLinkOnly()->create();

        $this->post(route('password.email'), ['email' => $user->email]);

        $token = null;
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use (&$token) {
            $token = $notification->token;

            return true;
        });

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $user->refresh();
        $this->assertNotNull($user->password);
        $this->assertAuthenticatedAs($user);
    }
}
