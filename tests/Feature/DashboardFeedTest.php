<?php

namespace Tests\Feature;

use App\Models\Circle;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $this->get(route('member.dashboard'))->assertRedirect(route('login'));
    }

    public function test_member_sees_posts_from_own_circles(): void
    {
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create();
        $adherent->circles()->attach($circle, ['joined_at' => now()]);

        $post = Post::factory()->for($circle)->create(['body' => 'Post du cercle inscrit.']);

        $this->actingAs($adherent)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee('Post du cercle inscrit.');
    }

    public function test_member_sees_general_feed_posts(): void
    {
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create();
        $post = Post::factory()->for($circle)->pushed()->create(['body' => 'Post général.']);

        $this->actingAs($adherent)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee('Post général.');
    }

    public function test_member_without_circles_sees_only_general_posts(): void
    {
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create();
        $generalPost = Post::factory()->for($circle)->pushed()->create(['body' => 'Publication générale.']);
        $circlePost = Post::factory()->for($circle)->create(['body' => 'Publication cercle seul.']);

        $this->actingAs($adherent)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee('Publication générale.')
            ->assertDontSee('Publication cercle seul.');
    }

    public function test_pushed_post_in_member_circle_appears_only_once(): void
    {
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create();
        $adherent->circles()->attach($circle, ['joined_at' => now()]);

        $post = Post::factory()->for($circle)->pushed()->create(['body' => 'Post poussé unique.']);

        $response = $this->actingAs($adherent)
            ->get(route('member.dashboard'))
            ->assertOk();

        $this->assertSame(
            1,
            substr_count($response->getContent(), 'Post poussé unique.')
        );
    }

    public function test_pushed_post_displays_general_label(): void
    {
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create();
        Post::factory()->for($circle)->pushed()->create(['body' => 'Contenu général.']);

        $this->actingAs($adherent)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee('Général');
    }

    public function test_circle_post_displays_circle_name_as_label(): void
    {
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create(['name' => 'Cercle Habitat']);
        $adherent->circles()->attach($circle, ['joined_at' => now()]);

        Post::factory()->for($circle)->create(['body' => 'Contenu du cercle.', 'pushed_to_general' => false]);

        $this->actingAs($adherent)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee('Cercle Habitat');
    }

    public function test_posts_not_in_member_circles_and_not_pushed_are_hidden(): void
    {
        $adherent = User::factory()->adherent()->create();
        $otherCircle = Circle::factory()->create();

        Post::factory()->for($otherCircle)->create(['body' => 'Post d\'un autre cercle.', 'pushed_to_general' => false]);

        $this->actingAs($adherent)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertDontSee('Post d\'un autre cercle.');
    }
}
