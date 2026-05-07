<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_logs_in_with_valid_credentials(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        $response = $this->post(route('login.password'), [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('member.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_redirects_to_dashboard_if_already_authenticated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('member.dashboard'));
    }

    public function test_rejects_wrong_password_with_generic_error(): void
    {
        $user = User::factory()->create(['password' => bcrypt('correct')]);

        $response = $this->post(route('login.password'), [
            'email' => $user->email,
            'password' => 'wrong',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email', null, 'password');
        $this->assertGuest();
    }

    public function test_shows_dedicated_error_when_account_has_no_password(): void
    {
        $user = User::factory()->magicLinkOnly()->create();

        $response = $this->post(route('login.password'), [
            'email' => $user->email,
            'password' => 'anything',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email', null, 'password');

        $errorMessage = $response->baseResponse
            ->getSession()
            ->get('errors')
            ->getBag('password')
            ->first('email');

        $this->assertStringContainsString('lien magique', $errorMessage);
        $this->assertGuest();
    }

    public function test_remembers_user_when_checkbox_checked(): void
    {
        $user = User::factory()->create(['password' => bcrypt('pass1234')]);

        $this->post(route('login.password'), [
            'email' => $user->email,
            'password' => 'pass1234',
            'remember' => '1',
        ]);

        $this->assertAuthenticatedAs($user);
    }
}
