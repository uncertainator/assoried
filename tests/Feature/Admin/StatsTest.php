<?php

namespace Tests\Feature\Admin;

use App\Models\Circle;
use App\Models\CircleMembership;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected(): void
    {
        $this->get(route('admin.stats'))
            ->assertRedirect(route('login'));
    }

    public function test_adherent_cannot_access_stats(): void
    {
        $this->actingAs(User::factory()->adherent()->create())
            ->get(route('admin.stats'))
            ->assertStatus(403);
    }

    public function test_referent_cannot_access_stats(): void
    {
        $this->actingAs(User::factory()->referent()->create())
            ->get(route('admin.stats'))
            ->assertStatus(403);
    }

    public function test_admin_can_access_stats(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.stats'))
            ->assertOk();
    }

    public function test_total_member_count_is_correct(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->count(3)->create();

        $this->actingAs($admin)
            ->get(route('admin.stats'))
            ->assertOk()
            ->assertSee('4');
    }

    public function test_membership_status_counts_are_correct(): void
    {
        $admin = User::factory()->admin()->create();
        $circle = Circle::factory()->create();

        CircleMembership::factory()->count(2)->state(['circle_id' => $circle->id])->pending()->create();
        CircleMembership::factory()->count(5)->state(['circle_id' => $circle->id])->approved()->create();
        CircleMembership::factory()->count(1)->state(['circle_id' => $circle->id])->rejected()->create();

        $response = $this->actingAs($admin)->get(route('admin.stats'));

        $response->assertOk();
        $response->assertSee('2'); // pending
        $response->assertSee('5'); // approved
        $response->assertSee('1'); // rejected
    }

    public function test_meetings_last_90_days_excludes_older_meetings(): void
    {
        $admin = User::factory()->admin()->create();
        $circle = Circle::factory()->create();

        Meeting::factory()->count(2)->state([
            'circle_id' => $circle->id,
            'scheduled_at' => now()->subDays(30),
        ])->create();

        Meeting::factory()->count(1)->state([
            'circle_id' => $circle->id,
            'scheduled_at' => now()->subDays(100),
        ])->create();

        $this->actingAs($admin)
            ->get(route('admin.stats'))
            ->assertOk()
            ->assertSee('2');
    }
}
