<?php

namespace Tests\Feature\Post;

use App\Models\Circle;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Accès au feed cercle
    // -----------------------------------------------------------------------

    public function test_authenticated_member_can_view_circle_feed(): void
    {
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create();

        $this->actingAs($adherent)
            ->get(route('member.circles.show', $circle))
            ->assertOk();
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $circle = Circle::factory()->create();

        $this->get(route('member.circles.show', $circle))
            ->assertRedirect(route('login'));
    }

    // -----------------------------------------------------------------------
    // Publication
    // -----------------------------------------------------------------------

    public function test_referent_can_publish_in_own_circle(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->post(route('member.circles.posts.store', $circle), ['body' => 'Contenu du post.'])
            ->assertRedirect(route('member.circles.show', $circle));

        $this->assertDatabaseHas('posts', [
            'circle_id' => $circle->id,
            'author_id' => $referent->id,
            'body' => 'Contenu du post.',
        ]);
    }

    public function test_admin_can_publish_in_any_circle(): void
    {
        $admin = User::factory()->admin()->create();
        $circle = Circle::factory()->create();

        $this->actingAs($admin)
            ->post(route('member.circles.posts.store', $circle), ['body' => 'Post admin.'])
            ->assertRedirect(route('member.circles.show', $circle));

        $this->assertDatabaseHas('posts', ['circle_id' => $circle->id, 'body' => 'Post admin.']);
    }

    public function test_adherent_cannot_publish(): void
    {
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create();

        $this->actingAs($adherent)
            ->post(route('member.circles.posts.store', $circle), ['body' => 'Tentative.'])
            ->assertForbidden();

        $this->assertDatabaseMissing('posts', ['body' => 'Tentative.']);
    }

    public function test_referent_cannot_publish_in_another_circle(): void
    {
        $referent = User::factory()->referent()->create();
        $ownCircle = Circle::factory()->create(['referent_id' => $referent->id]);
        $otherCircle = Circle::factory()->create();

        $this->actingAs($referent)
            ->post(route('member.circles.posts.store', $otherCircle), ['body' => 'Tentative.'])
            ->assertForbidden();
    }

    public function test_body_max_length_is_enforced(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->post(route('member.circles.posts.store', $circle), ['body' => str_repeat('a', 5001)])
            ->assertSessionHasErrors('body');
    }

    // -----------------------------------------------------------------------
    // Publication avec push immédiat
    // -----------------------------------------------------------------------

    public function test_post_published_with_push_is_in_general_feed(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $this->actingAs($referent)->post(route('member.circles.posts.store', $circle), [
            'body' => 'Post poussé.',
            'push_to_general' => '1',
        ]);

        $this->assertDatabaseHas('posts', [
            'body' => 'Post poussé.',
            'pushed_to_general' => true,
        ]);
    }

    // -----------------------------------------------------------------------
    // Push depuis la liste
    // -----------------------------------------------------------------------

    public function test_referent_can_push_existing_post_to_general_feed(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $post = Post::factory()->for($circle)->for($referent, 'author')->create();

        $this->actingAs($referent)
            ->patch(route('member.posts.push', $post))
            ->assertRedirect();

        $this->assertTrue($post->fresh()->pushed_to_general);
        $this->assertNotNull($post->fresh()->pushed_at);
    }

    public function test_admin_can_push_post_of_any_circle(): void
    {
        $admin = User::factory()->admin()->create();
        $circle = Circle::factory()->create();
        $post = Post::factory()->for($circle)->create();

        $this->actingAs($admin)
            ->patch(route('member.posts.push', $post))
            ->assertRedirect();

        $this->assertTrue($post->fresh()->pushed_to_general);
    }

    public function test_adherent_cannot_push_post(): void
    {
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create();
        $post = Post::factory()->for($circle)->create();

        $this->actingAs($adherent)
            ->patch(route('member.posts.push', $post))
            ->assertForbidden();
    }

    public function test_already_pushed_post_cannot_be_pushed_again(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $post = Post::factory()->for($circle)->for($referent, 'author')->pushed()->create();

        $this->actingAs($referent)
            ->patch(route('member.posts.push', $post))
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Suppression
    // -----------------------------------------------------------------------

    public function test_referent_can_delete_own_circle_post(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $post = Post::factory()->for($circle)->for($referent, 'author')->pushed()->create();

        $this->actingAs($referent)
            ->delete(route('member.posts.destroy', $post))
            ->assertRedirect(route('member.circles.show', $circle));

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_deleting_post_removes_it_from_general_feed(): void
    {
        $admin = User::factory()->admin()->create();
        $circle = Circle::factory()->create();
        $post = Post::factory()->for($circle)->pushed()->create();

        $this->actingAs($admin)->delete(route('member.posts.destroy', $post));

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_adherent_cannot_delete_post(): void
    {
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create();
        $post = Post::factory()->for($circle)->create();

        $this->actingAs($adherent)
            ->delete(route('member.posts.destroy', $post))
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Feed général
    // -----------------------------------------------------------------------

    public function test_general_feed_shows_only_pushed_posts(): void
    {
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create();
        $pushedPost = Post::factory()->for($circle)->pushed()->create(['body' => 'Visible.']);
        $normalPost = Post::factory()->for($circle)->create(['body' => 'Invisible.']);

        $response = $this->actingAs($adherent)->get(route('member.feed'));

        $response->assertOk()
            ->assertSee('Visible.')
            ->assertDontSee('Invisible.');
    }

    public function test_unauthenticated_user_cannot_access_general_feed(): void
    {
        $this->get(route('member.feed'))->assertRedirect(route('login'));
    }
}
