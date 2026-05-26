<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceModeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::updateOrCreate(['key' => 'maintenance_mode'], ['value' => '0']);
    }

    private function enableMaintenance(): void
    {
        Setting::set('maintenance_mode', '1');
    }

    // -----------------------------------------------------------------------
    // Visitors
    // -----------------------------------------------------------------------

    public function test_visitor_blocked_503_when_maintenance_on(): void
    {
        $this->enableMaintenance();

        $response = $this->get(route('home'));

        $response->assertStatus(503);
    }

    public function test_visitor_sees_maintenance_page_content(): void
    {
        $this->enableMaintenance();

        $response = $this->get(route('home'));

        $response->assertSee('maintenance');
        $response->assertSee(route('login'));
    }

    public function test_visitor_passes_when_maintenance_off(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
    }

    // -----------------------------------------------------------------------
    // Login route stays accessible
    // -----------------------------------------------------------------------

    public function test_login_route_accessible_when_maintenance_on(): void
    {
        $this->enableMaintenance();

        $response = $this->get(route('login'));

        $response->assertStatus(200);
    }

    public function test_magic_link_verify_accessible_when_maintenance_on(): void
    {
        $this->enableMaintenance();

        // Signed routes return 403 when signature is missing, not 503
        $response = $this->get('/auth/magic-link/verify');

        $response->assertStatus(403);
    }

    // -----------------------------------------------------------------------
    // Authenticated non-admin
    // -----------------------------------------------------------------------

    public function test_adherent_blocked_503_when_maintenance_on(): void
    {
        $this->enableMaintenance();
        $adherent = User::factory()->adherent()->create();

        $response = $this->actingAs($adherent)->get(route('member.dashboard'));

        $response->assertStatus(503);
    }

    public function test_referent_blocked_503_when_maintenance_on(): void
    {
        $this->enableMaintenance();
        $referent = User::factory()->referent()->create();

        $response = $this->actingAs($referent)->get(route('member.dashboard'));

        $response->assertStatus(503);
    }

    // -----------------------------------------------------------------------
    // Admin passes through
    // -----------------------------------------------------------------------

    public function test_admin_passes_through_when_maintenance_on(): void
    {
        $this->enableMaintenance();
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.members.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_home_when_maintenance_on(): void
    {
        $this->enableMaintenance();
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('home'));

        $response->assertStatus(200);
    }

    // -----------------------------------------------------------------------
    // Toggle
    // -----------------------------------------------------------------------

    public function test_toggle_enables_maintenance(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.maintenance.toggle'));

        $this->assertEquals('1', Setting::get('maintenance_mode'));
    }

    public function test_toggle_disables_maintenance(): void
    {
        $this->enableMaintenance();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.maintenance.toggle'));

        $this->assertEquals('0', Setting::get('maintenance_mode'));
    }

    public function test_toggle_redirects_back_with_success(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->from(route('admin.members.index'))
            ->post(route('admin.maintenance.toggle'));

        $response->assertRedirect(route('admin.members.index'));
        $response->assertSessionHas('success');
    }

    public function test_non_admin_cannot_toggle(): void
    {
        $adherent = User::factory()->adherent()->create();

        $response = $this->actingAs($adherent)->post(route('admin.maintenance.toggle'));

        $response->assertStatus(403);
    }
}
