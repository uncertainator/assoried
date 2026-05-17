<?php

namespace Tests\Feature;

use App\Models\Circle;
use App\Models\LabTool;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class LabToolTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Index — liste membre
    // -------------------------------------------------------

    public function test_guest_is_redirected_from_tools_index(): void
    {
        $this->get('/le-lab/outils')->assertRedirect('/connexion');
    }

    public function test_authenticated_member_can_view_tools_list(): void
    {
        $member = User::factory()->create();
        $tool = LabTool::factory()->create(['title' => 'Canvas Empathie']);

        $this->actingAs($member)
            ->get('/le-lab/outils')
            ->assertOk()
            ->assertSee('Canvas Empathie');
    }

    public function test_inactive_tool_is_not_visible_to_member(): void
    {
        $member = User::factory()->create();
        LabTool::factory()->inactive()->create(['title' => 'Outil secret']);

        $this->actingAs($member)
            ->get('/le-lab/outils')
            ->assertOk()
            ->assertDontSee('Outil secret');
    }

    public function test_tools_are_grouped_by_category(): void
    {
        $member = User::factory()->create();
        LabTool::factory()->create(['title' => 'Outil A', 'category' => 'Facilitation']);
        LabTool::factory()->create(['title' => 'Outil B', 'category' => 'Design Thinking']);

        $this->actingAs($member)
            ->get('/le-lab/outils')
            ->assertOk()
            ->assertSee('Facilitation')
            ->assertSee('Design Thinking');
    }

    // -------------------------------------------------------
    // Download — route signée
    // -------------------------------------------------------

    public function test_guest_cannot_download_tool(): void
    {
        $tool = LabTool::factory()->create();
        $signedUrl = URL::temporarySignedRoute('lab.tools.download', now()->addHour(), $tool);

        $this->get($signedUrl)->assertRedirect('/connexion');
    }

    public function test_authenticated_member_can_download_with_signed_url(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('lab-tools/fake-tool.pdf', 'fake pdf content');

        $member = User::factory()->create();
        $tool = LabTool::factory()->create(['file_path' => 'fake-tool.pdf']);

        $signedUrl = URL::temporarySignedRoute('lab.tools.download', now()->addHour(), $tool);

        $this->actingAs($member)
            ->get($signedUrl)
            ->assertOk();
    }

    public function test_download_increments_downloads_count(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('lab-tools/fake-tool.pdf', 'fake pdf content');

        $member = User::factory()->create();
        $tool = LabTool::factory()->create(['file_path' => 'fake-tool.pdf', 'downloads_count' => 0]);

        $signedUrl = URL::temporarySignedRoute('lab.tools.download', now()->addHour(), $tool);

        $this->actingAs($member)->get($signedUrl);

        $this->assertDatabaseHas('lab_tools', ['id' => $tool->id, 'downloads_count' => 1]);
    }

    public function test_download_without_signature_returns_403(): void
    {
        $member = User::factory()->create();
        $tool = LabTool::factory()->create();

        $this->actingAs($member)
            ->get("/le-lab/outils/{$tool->id}/download")
            ->assertForbidden();
    }

    // -------------------------------------------------------
    // Admin — index
    // -------------------------------------------------------

    public function test_admin_can_view_all_tools_including_inactive(): void
    {
        $admin = User::factory()->admin()->create();
        LabTool::factory()->create(['title' => 'Outil actif']);
        LabTool::factory()->inactive()->create(['title' => 'Outil inactif']);

        $this->actingAs($admin)
            ->get('/admin/lab/outils')
            ->assertOk()
            ->assertSee('Outil actif')
            ->assertSee('Outil inactif');
    }

    public function test_adherent_cannot_access_admin_tools_index(): void
    {
        $adherent = User::factory()->create();

        $this->actingAs($adherent)
            ->get('/admin/lab/outils')
            ->assertForbidden();
    }

    // -------------------------------------------------------
    // Admin — create
    // -------------------------------------------------------

    public function test_admin_can_access_create_form(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/lab/outils/create')
            ->assertOk();
    }

    public function test_adherent_cannot_access_create_form(): void
    {
        $adherent = User::factory()->create();

        $this->actingAs($adherent)
            ->get('/admin/lab/outils/create')
            ->assertForbidden();
    }

    public function test_lab_referent_can_access_create_form(): void
    {
        $referent = User::factory()->referent()->create();
        Circle::factory()->create(['slug' => 'lab', 'referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->get('/admin/lab/outils/create')
            ->assertOk();
    }

    public function test_other_referent_cannot_access_create_form(): void
    {
        $referent = User::factory()->referent()->create();
        Circle::factory()->create(['slug' => 'autre-cercle', 'referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->get('/admin/lab/outils/create')
            ->assertForbidden();
    }

    // -------------------------------------------------------
    // Admin — store
    // -------------------------------------------------------

    public function test_admin_can_store_tool_with_pdf(): void
    {
        Storage::fake('local');

        $admin = User::factory()->admin()->create();
        $file = UploadedFile::fake()->create('template.pdf', 500, 'application/pdf');

        $this->actingAs($admin)
            ->post('/admin/lab/outils', [
                'title' => 'Canvas Empathie',
                'description' => 'Un outil pour comprendre les utilisateurs.',
                'category' => 'Design Thinking',
                'active' => '1',
                'file' => $file,
            ])
            ->assertRedirect('/admin/lab/outils')
            ->assertSessionHas('success');

        $tool = LabTool::first();
        $this->assertNotNull($tool);
        $this->assertEquals('Canvas Empathie', $tool->title);
        $this->assertEquals($admin->id, $tool->created_by);
        Storage::disk('local')->assertExists('lab-tools/'.$tool->file_path);
    }

    public function test_adherent_cannot_store_tool(): void
    {
        $adherent = User::factory()->create();
        $file = UploadedFile::fake()->create('template.pdf', 100, 'application/pdf');

        $this->actingAs($adherent)
            ->post('/admin/lab/outils', [
                'title' => 'Test',
                'file' => $file,
            ])
            ->assertForbidden();
    }

    public function test_pdf_exceeding_20mb_fails_validation(): void
    {
        Storage::fake('local');

        $admin = User::factory()->admin()->create();
        $bigFile = UploadedFile::fake()->create('gros.pdf', 21000, 'application/pdf');

        $this->actingAs($admin)
            ->post('/admin/lab/outils', [
                'title' => 'Trop gros',
                'file' => $bigFile,
            ])
            ->assertSessionHasErrors('file');

        $this->assertDatabaseCount('lab_tools', 0);
    }

    public function test_non_pdf_file_fails_validation(): void
    {
        Storage::fake('local');

        $admin = User::factory()->admin()->create();
        $wrongFile = UploadedFile::fake()->create('image.png', 100, 'image/png');

        $this->actingAs($admin)
            ->post('/admin/lab/outils', [
                'title' => 'Mauvais format',
                'file' => $wrongFile,
            ])
            ->assertSessionHasErrors('file');

        $this->assertDatabaseCount('lab_tools', 0);
    }

    public function test_lab_referent_can_store_tool(): void
    {
        Storage::fake('local');

        $referent = User::factory()->referent()->create();
        Circle::factory()->create(['slug' => 'lab', 'referent_id' => $referent->id]);
        $file = UploadedFile::fake()->create('outil.pdf', 200, 'application/pdf');

        $this->actingAs($referent)
            ->post('/admin/lab/outils', [
                'title' => 'Outil Facilitation',
                'category' => 'Facilitation',
                'active' => '1',
                'file' => $file,
            ])
            ->assertRedirect('/admin/lab/outils');

        $this->assertDatabaseHas('lab_tools', ['title' => 'Outil Facilitation']);
    }

    // -------------------------------------------------------
    // Admin — update
    // -------------------------------------------------------

    public function test_admin_can_update_tool_without_changing_file(): void
    {
        $admin = User::factory()->admin()->create();
        $tool = LabTool::factory()->create(['title' => 'Ancien titre', 'file_path' => 'existing.pdf']);

        $this->actingAs($admin)
            ->put("/admin/lab/outils/{$tool->id}", [
                'title' => 'Nouveau titre',
                'category' => 'Facilitation',
                'active' => '1',
            ])
            ->assertRedirect('/admin/lab/outils')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('lab_tools', [
            'id' => $tool->id,
            'title' => 'Nouveau titre',
            'file_path' => 'existing.pdf',
        ]);
    }

    public function test_admin_can_update_tool_with_new_file(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('lab-tools/old-file.pdf', 'old content');

        $admin = User::factory()->admin()->create();
        $tool = LabTool::factory()->create(['file_path' => 'old-file.pdf']);
        $newFile = UploadedFile::fake()->create('nouveau.pdf', 300, 'application/pdf');

        $this->actingAs($admin)
            ->put("/admin/lab/outils/{$tool->id}", [
                'title' => $tool->title,
                'active' => '1',
                'file' => $newFile,
            ])
            ->assertRedirect('/admin/lab/outils');

        $tool->refresh();
        Storage::disk('local')->assertMissing('lab-tools/old-file.pdf');
        Storage::disk('local')->assertExists('lab-tools/'.$tool->file_path);
    }

    // -------------------------------------------------------
    // Admin — destroy
    // -------------------------------------------------------

    public function test_admin_can_delete_tool_and_file_is_removed(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('lab-tools/fake-tool.pdf', 'fake content');

        $admin = User::factory()->admin()->create();
        $tool = LabTool::factory()->create(['file_path' => 'fake-tool.pdf']);

        $this->actingAs($admin)
            ->delete("/admin/lab/outils/{$tool->id}")
            ->assertRedirect('/admin/lab/outils')
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('lab_tools', ['id' => $tool->id]);
        Storage::disk('local')->assertMissing('lab-tools/fake-tool.pdf');
    }

    public function test_adherent_cannot_delete_tool(): void
    {
        $adherent = User::factory()->create();
        $tool = LabTool::factory()->create();

        $this->actingAs($adherent)
            ->delete("/admin/lab/outils/{$tool->id}")
            ->assertForbidden();
    }
}
