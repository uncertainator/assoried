<?php

namespace Tests\Feature;

use App\Models\Circle;
use App\Models\CircleMembership;
use App\Models\Poll;
use App\Models\PollVote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PollTest extends TestCase
{
    use RefreshDatabase;

    // ----------------------------------------------------------------
    // Création
    // ----------------------------------------------------------------

    public function test_referent_can_create_poll_for_own_circle(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $response = $this->actingAs($referent)->post(route('member.circles.polls.store', $circle), [
            'title' => 'Sondage test',
            'type' => 'yes_no',
            'closes_at' => now()->addDays(7)->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('polls', [
            'circle_id' => $circle->id,
            'created_by' => $referent->id,
            'title' => 'Sondage test',
        ]);
    }

    public function test_adherent_cannot_create_poll(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $adherent = User::factory()->adherent()->create();

        $response = $this->actingAs($adherent)->post(route('member.circles.polls.store', $circle), [
            'title' => 'Sondage interdit',
            'type' => 'yes_no',
            'closes_at' => now()->addDays(7)->format('Y-m-d H:i:s'),
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('polls', ['title' => 'Sondage interdit']);
    }

    public function test_admin_can_create_association_poll(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('member.polls.store'), [
            'title' => 'Sondage association',
            'type' => 'yes_no',
            'closes_at' => now()->addDays(14)->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('polls', [
            'circle_id' => null,
            'title' => 'Sondage association',
        ]);
    }

    public function test_closes_at_must_be_in_the_future(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $response = $this->actingAs($referent)->post(route('member.circles.polls.store', $circle), [
            'title' => 'Sondage passé',
            'type' => 'yes_no',
            'closes_at' => now()->subDay()->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionHasErrors('closes_at');
        $this->assertDatabaseMissing('polls', ['title' => 'Sondage passé']);
    }

    // ----------------------------------------------------------------
    // Vote
    // ----------------------------------------------------------------

    public function test_approved_member_can_vote(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $member = User::factory()->adherent()->create();

        CircleMembership::factory()->approved()->create([
            'user_id' => $member->id,
            'circle_id' => $circle->id,
        ]);

        $poll = Poll::factory()->open()->create([
            'circle_id' => $circle->id,
            'created_by' => $referent->id,
        ]);

        $response = $this->actingAs($member)->post(route('member.polls.vote', $poll), [
            'choice' => 'oui',
        ]);

        $response->assertRedirect(route('member.polls.show', $poll));
        $this->assertDatabaseHas('poll_votes', [
            'poll_id' => $poll->id,
            'user_id' => $member->id,
            'choice' => 'oui',
        ]);
    }

    public function test_member_cannot_vote_twice(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $member = User::factory()->adherent()->create();

        CircleMembership::factory()->approved()->create([
            'user_id' => $member->id,
            'circle_id' => $circle->id,
        ]);

        $poll = Poll::factory()->open()->create([
            'circle_id' => $circle->id,
            'created_by' => $referent->id,
        ]);

        PollVote::create(['poll_id' => $poll->id, 'user_id' => $member->id, 'choice' => 'oui']);

        $response = $this->actingAs($member)->post(route('member.polls.vote', $poll), [
            'choice' => 'non',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('poll_votes', 1);
    }

    public function test_cannot_vote_on_closed_poll(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $member = User::factory()->adherent()->create();

        CircleMembership::factory()->approved()->create([
            'user_id' => $member->id,
            'circle_id' => $circle->id,
        ]);

        $poll = Poll::factory()->closed()->create([
            'circle_id' => $circle->id,
            'created_by' => $referent->id,
        ]);

        $response = $this->actingAs($member)->post(route('member.polls.vote', $poll), [
            'choice' => 'oui',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('poll_votes', 0);
    }

    public function test_non_member_cannot_vote_on_circle_poll(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $outsider = User::factory()->adherent()->create();

        $poll = Poll::factory()->open()->create([
            'circle_id' => $circle->id,
            'created_by' => $referent->id,
        ]);

        $response = $this->actingAs($outsider)->post(route('member.polls.vote', $poll), [
            'choice' => 'oui',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('poll_votes', 0);
    }

    // ----------------------------------------------------------------
    // Résultats
    // ----------------------------------------------------------------

    public function test_results_not_visible_on_open_poll(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $member = User::factory()->adherent()->create();

        CircleMembership::factory()->approved()->create([
            'user_id' => $member->id,
            'circle_id' => $circle->id,
        ]);

        $poll = Poll::factory()->open()->create([
            'circle_id' => $circle->id,
            'created_by' => $referent->id,
        ]);

        $this->assertEmpty($poll->results());
    }

    public function test_results_visible_after_closure(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $member = User::factory()->adherent()->create();

        $poll = Poll::factory()->closed()->create([
            'circle_id' => $circle->id,
            'created_by' => $referent->id,
        ]);

        PollVote::create(['poll_id' => $poll->id, 'user_id' => $member->id, 'choice' => 'oui']);

        $results = $poll->results();

        $this->assertEquals(1, $results['total']);
        $this->assertEquals(1, $results['breakdown']['oui']);
        $this->assertEquals(0, $results['breakdown']['non']);
    }

    // ----------------------------------------------------------------
    // Index
    // ----------------------------------------------------------------

    public function test_index_shows_own_circle_polls_and_association_polls(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $member = User::factory()->adherent()->create();

        CircleMembership::factory()->approved()->create([
            'user_id' => $member->id,
            'circle_id' => $circle->id,
        ]);

        $otherReferent = User::factory()->referent()->create();
        $otherCircle = Circle::factory()->create(['referent_id' => $otherReferent->id]);

        $circlePoll = Poll::factory()->open()->create(['circle_id' => $circle->id, 'created_by' => $referent->id]);
        $assoPoll = Poll::factory()->open()->create(['circle_id' => null, 'created_by' => $referent->id]);
        $otherCirclePoll = Poll::factory()->open()->create(['circle_id' => $otherCircle->id, 'created_by' => $otherReferent->id]);

        $response = $this->actingAs($member)->get(route('member.polls.index'));

        $response->assertOk();
        $response->assertSee($circlePoll->title);
        $response->assertSee($assoPoll->title);
        $response->assertDontSee($otherCirclePoll->title);
    }
}
