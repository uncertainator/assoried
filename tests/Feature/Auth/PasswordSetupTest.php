<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class PasswordSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_magic_link_user_without_password_redirected_to_setup(): void
    {
        $user = User::factory()->magicLinkOnly()->create();

        $url = URL::temporarySignedRoute(
            'auth.magic.verify',
            now()->addMinutes(15),
            ['email' => $user->email, 'circles' => []]
        );

        $response = $this->get($url);

        $response->assertRedirect(route('account.password.setup'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_magic_link_user_with_password_redirected_to_dashboard(): void
    {
        $user = User::factory()->create(['password' => bcrypt('existing')]);

        $url = URL::temporarySignedRoute(
            'auth.magic.verify',
            now()->addMinutes(15),
            ['email' => $user->email, 'circles' => []]
        );

        $response = $this->get($url);

        $response->assertRedirect(route('member.dashboard'));
    }

    public function test_user_who_dismissed_setup_redirected_to_dashboard(): void
    {
        $user = User::factory()->withDismissedSetup()->create();

        $url = URL::temporarySignedRoute(
            'auth.magic.verify',
            now()->addMinutes(15),
            ['email' => $user->email, 'circles' => []]
        );

        $response = $this->get($url);

        $response->assertRedirect(route('member.dashboard'));
    }

    public function test_setup_saves_password(): void
    {
        $user = User::factory()->magicLinkOnly()->create();

        $this->actingAs($user)
            ->post(route('account.password.store'), [
                'password' => 'newpass123',
                'password_confirmation' => 'newpass123',
            ])
            ->assertRedirect(route('member.dashboard'));

        $this->assertNotNull($user->fresh()->password);
    }

    public function test_dismiss_without_checkbox_does_not_set_dismissed_at(): void
    {
        $user = User::factory()->magicLinkOnly()->create();

        $this->actingAs($user)
            ->post(route('account.password.dismiss'), [])
            ->assertRedirect(route('member.dashboard'));

        $this->assertNull($user->fresh()->password_setup_dismissed_at);
    }

    public function test_dismiss_with_checkbox_sets_dismissed_at(): void
    {
        $user = User::factory()->magicLinkOnly()->create();

        $this->actingAs($user)
            ->post(route('account.password.dismiss'), ['dont_show_again' => '1'])
            ->assertRedirect(route('member.dashboard'));

        $this->assertNotNull($user->fresh()->password_setup_dismissed_at);
    }

    public function test_setup_page_requires_authentication(): void
    {
        $this->get(route('account.password.setup'))
            ->assertRedirect(route('login'));
    }
}
