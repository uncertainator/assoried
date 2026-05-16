<?php

namespace Tests\Feature\Member;

use App\Enums\MeetingReportStatus;
use App\Models\Circle;
use App\Models\CircleMembership;
use App\Models\Meeting;
use App\Models\MeetingAgendaItem;
use App\Models\MeetingReport;
use App\Models\User;
use App\Notifications\MeetingReportPublishedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MeetingReportTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Création d'un brouillon — référent de son cercle
    // -----------------------------------------------------------------------

    public function test_referent_cree_un_brouillon_pour_sa_reunion(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $meeting = Meeting::factory()->create(['circle_id' => $circle->id]);

        $this->actingAs($referent)
            ->post(route('member.meeting-reports.store', $meeting), [
                'participants' => 'Alice, Bob',
                'decisions' => ['Décision 1'],
                'open_points' => [],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('meeting_reports', [
            'meeting_id' => $meeting->id,
            'created_by' => $referent->id,
            'status' => MeetingReportStatus::Draft->value,
            'participants' => 'Alice, Bob',
        ]);
    }

    // -----------------------------------------------------------------------
    // Modification d'un brouillon — référent
    // -----------------------------------------------------------------------

    public function test_referent_modifie_un_brouillon(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $meeting = Meeting::factory()->create(['circle_id' => $circle->id]);
        $report = MeetingReport::factory()->draft()->create([
            'meeting_id' => $meeting->id,
            'created_by' => $referent->id,
            'participants' => 'Alice',
        ]);

        $this->actingAs($referent)
            ->put(route('member.meeting-reports.update', $report), [
                'participants' => 'Alice, Bob, Carol',
            ])
            ->assertRedirect(route('member.meeting-reports.show', $report))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('meeting_reports', [
            'id' => $report->id,
            'participants' => 'Alice, Bob, Carol',
        ]);
    }

    // -----------------------------------------------------------------------
    // Publication — référent
    // -----------------------------------------------------------------------

    public function test_referent_publie_un_brouillon_et_notifie_les_membres(): void
    {
        Notification::fake();

        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $meeting = Meeting::factory()->create(['circle_id' => $circle->id]);
        $member = User::factory()->adherent()->create();
        CircleMembership::factory()->approved()->create([
            'circle_id' => $circle->id,
            'user_id' => $member->id,
        ]);
        $report = MeetingReport::factory()->draft()->create([
            'meeting_id' => $meeting->id,
            'created_by' => $referent->id,
        ]);

        $this->actingAs($referent)
            ->post(route('member.meeting-reports.publish', $report))
            ->assertRedirect(route('member.meeting-reports.show', $report))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('meeting_reports', [
            'id' => $report->id,
            'status' => MeetingReportStatus::Published->value,
        ]);

        $this->assertNotNull($report->fresh()->published_at);

        Notification::assertSentTo($member, MeetingReportPublishedNotification::class);
    }

    // -----------------------------------------------------------------------
    // Double publication — erreur de validation
    // -----------------------------------------------------------------------

    public function test_referent_ne_peut_pas_publier_deux_cr_pour_la_meme_reunion(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $meeting = Meeting::factory()->create(['circle_id' => $circle->id]);

        MeetingReport::factory()->published()->create(['meeting_id' => $meeting->id]);

        $draft = MeetingReport::factory()->draft()->create([
            'meeting_id' => $meeting->id,
            'created_by' => $referent->id,
        ]);

        $this->actingAs($referent)
            ->post(route('member.meeting-reports.publish', $draft))
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('meeting_reports', [
            'id' => $draft->id,
            'status' => MeetingReportStatus::Draft->value,
        ]);
    }

    // -----------------------------------------------------------------------
    // Modification d'un CR publié — 403
    // -----------------------------------------------------------------------

    public function test_referent_ne_peut_pas_modifier_un_cr_publie(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $meeting = Meeting::factory()->create(['circle_id' => $circle->id]);
        $report = MeetingReport::factory()->published()->create([
            'meeting_id' => $meeting->id,
            'created_by' => $referent->id,
        ]);

        $this->actingAs($referent)
            ->put(route('member.meeting-reports.update', $report), [
                'participants' => 'Tentative de modification',
            ])
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Création — référent d'un autre cercle (403)
    // -----------------------------------------------------------------------

    public function test_referent_dun_autre_cercle_ne_peut_pas_creer_un_cr(): void
    {
        $referent = User::factory()->referent()->create();
        $autreReferent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $autreReferent->id]);
        $meeting = Meeting::factory()->create(['circle_id' => $circle->id]);

        $this->actingAs($referent)
            ->post(route('member.meeting-reports.store', $meeting), [
                'participants' => 'Tentative',
            ])
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Lecture — adhérent ne voit pas un brouillon (403)
    // -----------------------------------------------------------------------

    public function test_adherent_ne_peut_pas_voir_un_brouillon(): void
    {
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create();
        $meeting = Meeting::factory()->create(['circle_id' => $circle->id]);
        $report = MeetingReport::factory()->draft()->create(['meeting_id' => $meeting->id]);

        $this->actingAs($adherent)
            ->get(route('member.meeting-reports.show', $report))
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Lecture — adhérent voit un CR publié (200)
    // -----------------------------------------------------------------------

    public function test_adherent_peut_voir_un_cr_publie(): void
    {
        $adherent = User::factory()->adherent()->create();
        $circle = Circle::factory()->create();
        $meeting = Meeting::factory()->create(['circle_id' => $circle->id]);
        $report = MeetingReport::factory()->published()->create(['meeting_id' => $meeting->id]);
        CircleMembership::factory()->approved()->create([
            'circle_id' => $circle->id,
            'user_id' => $adherent->id,
        ]);

        $this->actingAs($adherent)
            ->get(route('member.meeting-reports.show', $report))
            ->assertOk();
    }

    // -----------------------------------------------------------------------
    // Suivi OdJ — pré-remplissage des points dans le formulaire
    // -----------------------------------------------------------------------

    public function test_formulaire_de_creation_preremplit_les_points_odj(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $meeting = Meeting::factory()->create(['circle_id' => $circle->id]);
        $item = MeetingAgendaItem::factory()->create([
            'meeting_id' => $meeting->id,
            'position' => 1,
            'title' => 'Point important',
        ]);

        $this->actingAs($referent)
            ->get(route('member.meeting-reports.create', $meeting))
            ->assertOk()
            ->assertSee('Point important');
    }

    // -----------------------------------------------------------------------
    // Admin — peut créer un CR dans n'importe quel cercle
    // -----------------------------------------------------------------------

    public function test_admin_peut_creer_un_brouillon(): void
    {
        $admin = User::factory()->admin()->create();
        $circle = Circle::factory()->create();
        $meeting = Meeting::factory()->create(['circle_id' => $circle->id]);

        $this->actingAs($admin)
            ->post(route('member.meeting-reports.store', $meeting), [
                'participants' => 'Admin présent',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('meeting_reports', [
            'meeting_id' => $meeting->id,
            'status' => MeetingReportStatus::Draft->value,
        ]);
    }
}
