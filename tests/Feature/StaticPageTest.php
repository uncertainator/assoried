<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaticPageTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Affichage public
    // -----------------------------------------------------------------------

    public function test_public_can_view_page(): void
    {
        Page::factory()->create(['slug' => 'mentions-legales', 'title' => 'Mentions légales', 'content' => 'Contenu test.']);

        $this->get(route('pages.show', 'mentions-legales'))
            ->assertOk()
            ->assertSee('Mentions légales');
    }

    public function test_unknown_slug_returns_404(): void
    {
        $this->get(route('pages.show', 'slug-inexistant'))
            ->assertNotFound();
    }

    // -----------------------------------------------------------------------
    // Admin — accès et modification
    // -----------------------------------------------------------------------

    public function test_admin_can_view_edit_form(): void
    {
        $admin = User::factory()->admin()->create();
        $page = Page::factory()->create(['slug' => 'a-propos', 'title' => 'À propos']);

        $this->actingAs($admin)
            ->get(route('admin.pages.edit', $page))
            ->assertOk()
            ->assertSee('À propos');
    }

    public function test_admin_can_update_page(): void
    {
        $admin = User::factory()->admin()->create();
        $page = Page::factory()->create(['slug' => 'a-propos', 'title' => 'À propos', 'content' => 'Ancien contenu.']);

        $this->actingAs($admin)
            ->put(route('admin.pages.update', $page), [
                'title' => 'À propos de nous',
                'content' => 'Nouveau contenu.',
            ])
            ->assertRedirect(route('admin.pages.index'));

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'title' => 'À propos de nous',
            'content' => 'Nouveau contenu.',
            'updated_by' => $admin->id,
        ]);
    }

    public function test_non_admin_cannot_access_edit_form(): void
    {
        $adherent = User::factory()->adherent()->create();
        $page = Page::factory()->create(['slug' => 'a-propos']);

        $this->actingAs($adherent)
            ->get(route('admin.pages.edit', $page))
            ->assertForbidden();
    }

    public function test_non_admin_cannot_update_page(): void
    {
        $adherent = User::factory()->adherent()->create();
        $page = Page::factory()->create(['slug' => 'a-propos', 'content' => 'Original.']);

        $this->actingAs($adherent)
            ->put(route('admin.pages.update', $page), [
                'title' => 'Hack',
                'content' => 'Contenu injecté.',
            ])
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Validation
    // -----------------------------------------------------------------------

    public function test_update_fails_when_title_missing(): void
    {
        $admin = User::factory()->admin()->create();
        $page = Page::factory()->create(['slug' => 'a-propos']);

        $this->actingAs($admin)
            ->put(route('admin.pages.update', $page), ['title' => '', 'content' => 'Contenu.'])
            ->assertSessionHasErrors(['title']);
    }
}
