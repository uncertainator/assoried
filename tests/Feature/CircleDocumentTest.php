<?php

namespace Tests\Feature;

use App\Models\Circle;
use App\Models\CircleDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CircleDocumentTest extends TestCase
{
    use RefreshDatabase;

    private function attachMember(Circle $circle, User $user): void
    {
        $circle->users()->attach($user, ['status' => 'approved', 'joined_at' => now()]);
    }

    // ----------------------------------------------------------------
    // viewAny — accès à la bibliothèque
    // ----------------------------------------------------------------

    public function test_approved_member_can_view_document_library(): void
    {
        $circle = Circle::factory()->create();
        $member = User::factory()->adherent()->create();
        $this->attachMember($circle, $member);
        $document = CircleDocument::factory()->forCircle($circle)->create();

        $this->actingAs($member)
            ->get(route('member.circles.documents.index', $circle))
            ->assertOk()
            ->assertSee($document->title);
    }

    public function test_non_member_cannot_view_document_library(): void
    {
        $circle = Circle::factory()->create();
        $outsider = User::factory()->adherent()->create();

        $this->actingAs($outsider)
            ->get(route('member.circles.documents.index', $circle))
            ->assertForbidden();
    }

    public function test_referent_can_view_own_circle_library(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->get(route('member.circles.documents.index', $circle))
            ->assertOk();
    }

    public function test_admin_can_view_any_circle_library(): void
    {
        $admin = User::factory()->admin()->create();
        $circle = Circle::factory()->create();
        CircleDocument::factory()->forCircle($circle)->create();

        $this->actingAs($admin)
            ->get(route('member.circles.documents.index', $circle))
            ->assertOk();
    }

    // ----------------------------------------------------------------
    // Filtre par tag et ordre
    // ----------------------------------------------------------------

    public function test_tag_filter_reduces_the_list(): void
    {
        $circle = Circle::factory()->create();
        $member = User::factory()->adherent()->create();
        $this->attachMember($circle, $member);

        CircleDocument::factory()->forCircle($circle)->withTags(['statuts'])->create(['title' => 'Document A']);
        CircleDocument::factory()->forCircle($circle)->withTags(['réunion'])->create(['title' => 'Document B']);

        $this->actingAs($member)
            ->get(route('member.circles.documents.index', [$circle, 'tag' => 'statuts']))
            ->assertOk()
            ->assertSee('Document A')
            ->assertDontSee('Document B');
    }

    public function test_documents_are_ordered_by_date_descending(): void
    {
        $circle = Circle::factory()->create();
        $member = User::factory()->adherent()->create();
        $this->attachMember($circle, $member);

        $older = CircleDocument::factory()->forCircle($circle)->create([
            'title' => 'Ancien document',
            'document_date' => '2024-01-01',
        ]);
        $newer = CircleDocument::factory()->forCircle($circle)->create([
            'title' => 'Document récent',
            'document_date' => '2025-06-01',
        ]);

        $this->actingAs($member)
            ->get(route('member.circles.documents.index', $circle))
            ->assertSeeInOrder([$newer->title, $older->title]);
    }

    // ----------------------------------------------------------------
    // create — accès au formulaire
    // ----------------------------------------------------------------

    public function test_circle_referent_can_access_create_form(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->get(route('referent.circle.documents.create', $circle))
            ->assertOk();
    }

    public function test_referent_of_another_circle_cannot_access_create_form(): void
    {
        $circle = Circle::factory()->create();
        $otherReferent = User::factory()->referent()->create();

        $this->actingAs($otherReferent)
            ->get(route('referent.circle.documents.create', $circle))
            ->assertForbidden();
    }

    public function test_adherent_cannot_access_create_form(): void
    {
        $circle = Circle::factory()->create();
        $member = User::factory()->adherent()->create();
        $this->attachMember($circle, $member);

        $this->actingAs($member)
            ->get(route('referent.circle.documents.create', $circle))
            ->assertForbidden();
    }

    // ----------------------------------------------------------------
    // store — upload PDF
    // ----------------------------------------------------------------

    public function test_referent_can_upload_pdf_document(): void
    {
        Storage::fake('public');

        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $file = UploadedFile::fake()->create('rapport.pdf', 500, 'application/pdf');

        $this->actingAs($referent)
            ->post(route('referent.circle.documents.store', $circle), [
                'title' => 'Rapport annuel',
                'type' => 'pdf',
                'document_date' => '2025-03-01',
                'file' => $file,
                'tags_input' => 'rapport, finances',
            ])
            ->assertRedirect(route('member.circles.documents.index', $circle));

        $this->assertDatabaseHas('circle_documents', [
            'circle_id' => $circle->id,
            'title' => 'Rapport annuel',
            'type' => 'pdf',
            'uploaded_by' => $referent->id,
        ]);

        $doc = CircleDocument::first();
        Storage::disk('public')->assertExists($doc->file_path);
        $this->assertNotNull($doc->original_filename);
        $this->assertStringEndsWith('.pdf', $doc->file_path);
    }

    public function test_pdf_filename_is_randomized(): void
    {
        Storage::fake('public');

        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $file = UploadedFile::fake()->create('mon-fichier.pdf', 100, 'application/pdf');

        $this->actingAs($referent)
            ->post(route('referent.circle.documents.store', $circle), [
                'title' => 'Test',
                'type' => 'pdf',
                'document_date' => '2025-01-01',
                'file' => $file,
            ]);

        $doc = CircleDocument::first();
        $this->assertEquals('mon-fichier.pdf', $doc->original_filename);
        $this->assertStringNotContainsString('mon-fichier', $doc->file_path);
    }

    public function test_pdf_submission_without_file_fails_validation(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->post(route('referent.circle.documents.store', $circle), [
                'title' => 'Sans fichier',
                'type' => 'pdf',
                'document_date' => '2025-01-01',
            ])
            ->assertSessionHasErrors('file');

        $this->assertDatabaseCount('circle_documents', 0);
    }

    public function test_pdf_exceeding_10mb_fails_validation(): void
    {
        Storage::fake('public');

        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $bigFile = UploadedFile::fake()->create('gros-fichier.pdf', 12288, 'application/pdf');

        $this->actingAs($referent)
            ->post(route('referent.circle.documents.store', $circle), [
                'title' => 'Gros fichier',
                'type' => 'pdf',
                'document_date' => '2025-01-01',
                'file' => $bigFile,
            ])
            ->assertSessionHasErrors('file');

        $this->assertDatabaseCount('circle_documents', 0);
    }

    // ----------------------------------------------------------------
    // store — lien externe
    // ----------------------------------------------------------------

    public function test_referent_can_store_link_document(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->post(route('referent.circle.documents.store', $circle), [
                'title' => 'Site officiel',
                'type' => 'link',
                'document_date' => '2025-01-15',
                'url' => 'https://example.com',
            ])
            ->assertRedirect(route('member.circles.documents.index', $circle));

        $this->assertDatabaseHas('circle_documents', [
            'type' => 'link',
            'url' => 'https://example.com',
        ]);
    }

    public function test_link_submission_without_url_fails_validation(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->post(route('referent.circle.documents.store', $circle), [
                'title' => 'Sans URL',
                'type' => 'link',
                'document_date' => '2025-01-01',
            ])
            ->assertSessionHasErrors('url');
    }

    public function test_link_with_invalid_url_fails_validation(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->post(route('referent.circle.documents.store', $circle), [
                'title' => 'URL invalide',
                'type' => 'link',
                'document_date' => '2025-01-01',
                'url' => 'pas-une-url',
            ])
            ->assertSessionHasErrors('url');
    }

    public function test_referent_of_another_circle_cannot_store_document(): void
    {
        $circle = Circle::factory()->create();
        $otherReferent = User::factory()->referent()->create();

        $this->actingAs($otherReferent)
            ->post(route('referent.circle.documents.store', $circle), [
                'title' => 'Tentative',
                'type' => 'link',
                'document_date' => '2025-01-01',
                'url' => 'https://example.com',
            ])
            ->assertForbidden();
    }

    // ----------------------------------------------------------------
    // destroy — suppression
    // ----------------------------------------------------------------

    public function test_referent_can_delete_pdf_and_removes_file_from_disk(): void
    {
        Storage::fake('public');

        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $document = CircleDocument::factory()->forCircle($circle)->asPdf()->create([
            'uploaded_by' => $referent->id,
        ]);
        Storage::disk('public')->put($document->file_path, 'fake pdf content');

        $this->actingAs($referent)
            ->delete(route('referent.circle.documents.destroy', [$circle, $document]))
            ->assertRedirect(route('member.circles.documents.index', $circle));

        $this->assertDatabaseMissing('circle_documents', ['id' => $document->id]);
        Storage::disk('public')->assertMissing($document->file_path);
    }

    public function test_referent_can_delete_link_document(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $document = CircleDocument::factory()->forCircle($circle)->asLink()->create([
            'uploaded_by' => $referent->id,
        ]);

        $this->actingAs($referent)
            ->delete(route('referent.circle.documents.destroy', [$circle, $document]))
            ->assertRedirect();

        $this->assertDatabaseMissing('circle_documents', ['id' => $document->id]);
    }

    public function test_adherent_cannot_delete_document(): void
    {
        $circle = Circle::factory()->create();
        $member = User::factory()->adherent()->create();
        $this->attachMember($circle, $member);
        $document = CircleDocument::factory()->forCircle($circle)->create();

        $this->actingAs($member)
            ->delete(route('referent.circle.documents.destroy', [$circle, $document]))
            ->assertForbidden();
    }

    public function test_referent_of_another_circle_cannot_delete_document(): void
    {
        $circle = Circle::factory()->create();
        $document = CircleDocument::factory()->forCircle($circle)->create();
        $otherReferent = User::factory()->referent()->create();

        $this->actingAs($otherReferent)
            ->delete(route('referent.circle.documents.destroy', [$circle, $document]))
            ->assertForbidden();
    }

    public function test_admin_can_delete_any_document(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $circle = Circle::factory()->create();
        $document = CircleDocument::factory()->forCircle($circle)->asLink()->create();

        $this->actingAs($admin)
            ->delete(route('referent.circle.documents.destroy', [$circle, $document]))
            ->assertRedirect();

        $this->assertDatabaseMissing('circle_documents', ['id' => $document->id]);
    }
}
