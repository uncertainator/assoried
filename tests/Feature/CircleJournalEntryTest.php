<?php

namespace Tests\Feature;

use App\Models\Circle;
use App\Models\CircleJournalEntry;
use App\Models\CircleMembership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CircleJournalEntryTest extends TestCase
{
    use RefreshDatabase;

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    private function circleWithReferent(): array
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        return [$circle, $referent];
    }

    private function approvedMember(Circle $circle): User
    {
        $member = User::factory()->adherent()->create();
        CircleMembership::factory()->approved()->create([
            'user_id' => $member->id,
            'circle_id' => $circle->id,
        ]);

        return $member;
    }

    private function validPayload(?string $date = null): array
    {
        return [
            'title' => 'Compte-rendu de réunion',
            'content' => 'Nous avons discuté des points suivants...',
            'entry_date' => $date ?? now()->format('Y-m-d'),
        ];
    }

    private function entry(Circle $circle, User $author): CircleJournalEntry
    {
        return CircleJournalEntry::factory()->create([
            'circle_id' => $circle->id,
            'created_by' => $author->id,
            'entry_date' => now()->format('Y-m-d'),
        ]);
    }

    // ----------------------------------------------------------------
    // 1. Accès à la liste (index)
    // ----------------------------------------------------------------

    public function test_referent_can_view_journal_index_of_their_circle(): void
    {
        [$circle, $referent] = $this->circleWithReferent();

        $this->actingAs($referent)
            ->get(route('member.circles.journal.index', $circle))
            ->assertOk();
    }

    public function test_approved_member_can_view_journal_index(): void
    {
        [$circle] = $this->circleWithReferent();
        $member = $this->approvedMember($circle);

        $this->actingAs($member)
            ->get(route('member.circles.journal.index', $circle))
            ->assertOk();
    }

    public function test_admin_can_view_any_circle_journal_index(): void
    {
        [$circle] = $this->circleWithReferent();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('member.circles.journal.index', $circle))
            ->assertOk();
    }

    public function test_non_member_cannot_view_journal_index(): void
    {
        [$circle] = $this->circleWithReferent();
        $outsider = User::factory()->adherent()->create();

        $this->actingAs($outsider)
            ->get(route('member.circles.journal.index', $circle))
            ->assertForbidden();
    }

    public function test_referent_of_another_circle_cannot_view_journal_index(): void
    {
        [$circle] = $this->circleWithReferent();
        $otherReferent = User::factory()->referent()->create();
        Circle::factory()->create(['referent_id' => $otherReferent->id]);

        $this->actingAs($otherReferent)
            ->get(route('member.circles.journal.index', $circle))
            ->assertForbidden();
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        [$circle] = $this->circleWithReferent();

        $this->get(route('member.circles.journal.index', $circle))
            ->assertRedirect(route('login'));
    }

    // ----------------------------------------------------------------
    // 2. Création d'une entrée
    // ----------------------------------------------------------------

    public function test_referent_can_create_journal_entry(): void
    {
        [$circle, $referent] = $this->circleWithReferent();
        $payload = $this->validPayload();

        $response = $this->actingAs($referent)
            ->post(route('member.circles.journal.store', $circle), $payload);

        $response->assertRedirect(route('member.circles.journal.index', $circle));
        $this->assertDatabaseHas('circle_journal_entries', [
            'circle_id' => $circle->id,
            'created_by' => $referent->id,
            'title' => $payload['title'],
        ]);
        $entry = CircleJournalEntry::where('circle_id', $circle->id)->first();
        $this->assertSame($payload['entry_date'], $entry->entry_date->format('Y-m-d'));
    }

    public function test_adherent_cannot_create_journal_entry(): void
    {
        [$circle] = $this->circleWithReferent();
        $member = $this->approvedMember($circle);

        $this->actingAs($member)
            ->post(route('member.circles.journal.store', $circle), $this->validPayload())
            ->assertForbidden();
    }

    public function test_referent_of_another_circle_cannot_create_entry_in_this_circle(): void
    {
        [$circle] = $this->circleWithReferent();
        $otherReferent = User::factory()->referent()->create();
        Circle::factory()->create(['referent_id' => $otherReferent->id]);

        $this->actingAs($otherReferent)
            ->post(route('member.circles.journal.store', $circle), $this->validPayload())
            ->assertForbidden();
    }

    // ----------------------------------------------------------------
    // 3. Validation
    // ----------------------------------------------------------------

    public function test_store_fails_when_title_is_empty(): void
    {
        [$circle, $referent] = $this->circleWithReferent();

        $this->actingAs($referent)
            ->post(route('member.circles.journal.store', $circle), array_merge($this->validPayload(), ['title' => '']))
            ->assertSessionHasErrors('title');
    }

    public function test_store_fails_when_content_exceeds_10000_characters(): void
    {
        [$circle, $referent] = $this->circleWithReferent();

        $this->actingAs($referent)
            ->post(route('member.circles.journal.store', $circle), array_merge($this->validPayload(), ['content' => str_repeat('x', 10001)]))
            ->assertSessionHasErrors('content');
    }

    // ----------------------------------------------------------------
    // 4. Modification
    // ----------------------------------------------------------------

    public function test_referent_can_update_their_own_entry(): void
    {
        [$circle, $referent] = $this->circleWithReferent();
        $entry = $this->entry($circle, $referent);

        $this->actingAs($referent)
            ->put(route('member.circles.journal.update', [$circle, $entry]), array_merge($this->validPayload(), ['title' => 'Titre modifié']))
            ->assertRedirect(route('member.circles.journal.index', $circle));

        $this->assertDatabaseHas('circle_journal_entries', [
            'id' => $entry->id,
            'title' => 'Titre modifié',
        ]);
    }

    public function test_admin_can_update_any_entry(): void
    {
        [$circle, $referent] = $this->circleWithReferent();
        $entry = $this->entry($circle, $referent);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('member.circles.journal.update', [$circle, $entry]), array_merge($this->validPayload(), ['title' => 'Modifié par admin']))
            ->assertRedirect(route('member.circles.journal.index', $circle));

        $this->assertDatabaseHas('circle_journal_entries', [
            'id' => $entry->id,
            'title' => 'Modifié par admin',
        ]);
    }

    public function test_referent_cannot_update_entry_from_another_author(): void
    {
        [$circle, $referent] = $this->circleWithReferent();
        $admin = User::factory()->admin()->create();
        $entry = $this->entry($circle, $admin);

        $this->actingAs($referent)
            ->put(route('member.circles.journal.update', [$circle, $entry]), $this->validPayload())
            ->assertForbidden();
    }

    // ----------------------------------------------------------------
    // 5. Suppression (soft delete)
    // ----------------------------------------------------------------

    public function test_referent_can_soft_delete_their_own_entry(): void
    {
        [$circle, $referent] = $this->circleWithReferent();
        $entry = $this->entry($circle, $referent);

        $this->actingAs($referent)
            ->delete(route('member.circles.journal.destroy', [$circle, $entry]))
            ->assertRedirect(route('member.circles.journal.index', $circle));

        $this->assertSoftDeleted('circle_journal_entries', ['id' => $entry->id]);
    }

    public function test_admin_can_soft_delete_any_entry(): void
    {
        [$circle, $referent] = $this->circleWithReferent();
        $entry = $this->entry($circle, $referent);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete(route('member.circles.journal.destroy', [$circle, $entry]))
            ->assertRedirect(route('member.circles.journal.index', $circle));

        $this->assertSoftDeleted('circle_journal_entries', ['id' => $entry->id]);
    }

    public function test_adherent_cannot_delete_an_entry(): void
    {
        [$circle, $referent] = $this->circleWithReferent();
        $entry = $this->entry($circle, $referent);
        $member = $this->approvedMember($circle);

        $this->actingAs($member)
            ->delete(route('member.circles.journal.destroy', [$circle, $entry]))
            ->assertForbidden();
    }

    public function test_soft_deleted_entry_does_not_appear_in_index(): void
    {
        [$circle, $referent] = $this->circleWithReferent();
        $entry = $this->entry($circle, $referent);
        $entry->delete();

        $this->actingAs($referent)
            ->get(route('member.circles.journal.index', $circle))
            ->assertOk()
            ->assertDontSee($entry->title);
    }

    // ----------------------------------------------------------------
    // 6. Ordre antichronologique
    // ----------------------------------------------------------------

    public function test_entries_are_ordered_antichronologically_by_entry_date(): void
    {
        [$circle, $referent] = $this->circleWithReferent();

        $older = CircleJournalEntry::factory()->create([
            'circle_id' => $circle->id,
            'created_by' => $referent->id,
            'title' => 'Entrée ancienne',
            'entry_date' => now()->subDays(10)->format('Y-m-d'),
        ]);

        $newer = CircleJournalEntry::factory()->create([
            'circle_id' => $circle->id,
            'created_by' => $referent->id,
            'title' => 'Entrée récente',
            'entry_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($referent)
            ->get(route('member.circles.journal.index', $circle))
            ->assertOk();

        $content = $response->getContent();
        $this->assertGreaterThan(
            strpos($content, $newer->title),
            strpos($content, $older->title),
            'Les entrées doivent être affichées de la plus récente à la plus ancienne.'
        );
    }
}
