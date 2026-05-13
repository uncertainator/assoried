<?php

namespace Tests\Feature;

use App\Enums\LabRequestStatus;
use App\Models\Circle;
use App\Models\LabExternalRequest;
use App\Models\User;
use App\Notifications\LabExternalRequestConfirmation;
use App\Notifications\LabExternalRequestReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LabExternalRequestTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Accès aux pages publiques
    // -------------------------------------------------------

    public function test_citoyen_page_is_accessible_without_auth(): void
    {
        $this->get(route('lab.external.citoyen'))
            ->assertOk();
    }

    public function test_entreprise_page_is_accessible_without_auth(): void
    {
        $this->get(route('lab.external.entreprise'))
            ->assertOk();
    }

    // -------------------------------------------------------
    // Soumission formulaire citoyen
    // -------------------------------------------------------

    public function test_citoyen_request_is_stored_on_valid_submission(): void
    {
        Notification::fake();

        $this->post(route('lab.external.citoyen.store'), [
            'nom_contact' => 'Marie Dupont',
            'email' => 'marie@exemple.fr',
            'territoire' => 'Sélestat',
            'message' => 'Je souhaite monter un projet de jardin partagé.',
            'rgpd_consent' => '1',
        ])
            ->assertRedirect(route('lab.external.confirmation'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('lab_external_requests', [
            'type' => 'citoyen',
            'nom_contact' => 'Marie Dupont',
            'email' => 'marie@exemple.fr',
            'territoire' => 'Sélestat',
            'statut' => LabRequestStatus::Nouvelle->value,
            'rgpd_consent' => true,
        ]);
    }

    // -------------------------------------------------------
    // Soumission formulaire entreprise
    // -------------------------------------------------------

    public function test_entreprise_request_is_stored_on_valid_submission(): void
    {
        Notification::fake();

        $this->post(route('lab.external.entreprise.store'), [
            'raison_sociale' => 'Acme SAS',
            'nom_contact' => 'Jean Martin',
            'email' => 'jean@acme.fr',
            'telephone' => '03 88 12 34 56',
            'besoin_type' => 'innovation',
            'message' => 'Nous cherchons à innover dans nos process internes.',
            'rgpd_consent' => '1',
        ])
            ->assertRedirect(route('lab.external.confirmation'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('lab_external_requests', [
            'type' => 'entreprise',
            'raison_sociale' => 'Acme SAS',
            'nom_contact' => 'Jean Martin',
            'email' => 'jean@acme.fr',
            'besoin_type' => 'innovation',
            'statut' => LabRequestStatus::Nouvelle->value,
        ]);
    }

    // -------------------------------------------------------
    // Validation RGPD
    // -------------------------------------------------------

    public function test_citoyen_submission_rejected_without_rgpd_consent(): void
    {
        $this->post(route('lab.external.citoyen.store'), [
            'nom_contact' => 'Marie Dupont',
            'email' => 'marie@exemple.fr',
            'message' => 'Un message valide.',
            // rgpd_consent absent
        ])
            ->assertSessionHasErrors('rgpd_consent');

        $this->assertDatabaseCount('lab_external_requests', 0);
    }

    public function test_entreprise_submission_rejected_without_rgpd_consent(): void
    {
        $this->post(route('lab.external.entreprise.store'), [
            'raison_sociale' => 'Acme SAS',
            'nom_contact' => 'Jean Martin',
            'email' => 'jean@acme.fr',
            'telephone' => '03 88 12 34 56',
            'message' => 'Un message valide.',
            // rgpd_consent absent
        ])
            ->assertSessionHasErrors('rgpd_consent');

        $this->assertDatabaseCount('lab_external_requests', 0);
    }

    // -------------------------------------------------------
    // Honeypot anti-spam
    // -------------------------------------------------------

    public function test_honeypot_rejects_spam_submission(): void
    {
        $this->post(route('lab.external.citoyen.store'), [
            'nom_contact' => 'Bot',
            'email' => 'bot@spam.com',
            'message' => 'Spam message.',
            'rgpd_consent' => '1',
            '_pot' => 'filled by bot',
        ])
            ->assertSessionHasErrors('_pot');

        $this->assertDatabaseCount('lab_external_requests', 0);
    }

    // -------------------------------------------------------
    // Notifications
    // -------------------------------------------------------

    public function test_notification_sent_to_lab_referents_and_admins_on_citoyen_submit(): void
    {
        Notification::fake();

        $labReferent = User::factory()->referent()->create();
        Circle::factory()->create(['slug' => 'lab', 'referent_id' => $labReferent->id]);

        $admin = User::factory()->admin()->create();

        $this->post(route('lab.external.citoyen.store'), [
            'nom_contact' => 'Marie Dupont',
            'email' => 'marie@exemple.fr',
            'message' => 'Mon projet citoyen.',
            'rgpd_consent' => '1',
        ]);

        Notification::assertSentTo($labReferent, LabExternalRequestReceived::class);
        Notification::assertSentTo($admin, LabExternalRequestReceived::class);
    }

    public function test_notification_not_sent_to_non_lab_referent(): void
    {
        Notification::fake();

        $otherReferent = User::factory()->referent()->create();
        Circle::factory()->create(['slug' => 'sport', 'referent_id' => $otherReferent->id]);

        $this->post(route('lab.external.citoyen.store'), [
            'nom_contact' => 'Marie Dupont',
            'email' => 'marie@exemple.fr',
            'message' => 'Mon projet citoyen.',
            'rgpd_consent' => '1',
        ]);

        Notification::assertNotSentTo($otherReferent, LabExternalRequestReceived::class);
    }

    public function test_confirmation_email_sent_to_requester(): void
    {
        Notification::fake();

        $this->post(route('lab.external.citoyen.store'), [
            'nom_contact' => 'Marie Dupont',
            'email' => 'marie@exemple.fr',
            'message' => 'Mon projet citoyen.',
            'rgpd_consent' => '1',
        ]);

        Notification::assertSentOnDemand(
            LabExternalRequestConfirmation::class,
            fn ($notification, $channels, $notifiable) => $notifiable->routes['mail'] === 'marie@exemple.fr'
        );
    }

    // -------------------------------------------------------
    // Accès liste /lab/external-requests
    // -------------------------------------------------------

    public function test_lab_referent_can_access_external_requests_list(): void
    {
        $labReferent = User::factory()->referent()->create();
        Circle::factory()->create(['slug' => 'lab', 'referent_id' => $labReferent->id]);

        $this->actingAs($labReferent)
            ->get(route('lab.external.index'))
            ->assertOk();
    }

    public function test_admin_can_access_external_requests_list(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('lab.external.index'))
            ->assertOk();
    }

    public function test_adherent_gets_403_on_external_requests_list(): void
    {
        $adherent = User::factory()->adherent()->create();

        $this->actingAs($adherent)
            ->get(route('lab.external.index'))
            ->assertForbidden();
    }

    public function test_other_circle_referent_gets_403_on_external_requests_list(): void
    {
        $referent = User::factory()->referent()->create();
        Circle::factory()->create(['slug' => 'environnement', 'referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->get(route('lab.external.index'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_external_requests_list(): void
    {
        $this->get(route('lab.external.index'))
            ->assertRedirect(route('login'));
    }

    // -------------------------------------------------------
    // Mise à jour du statut
    // -------------------------------------------------------

    public function test_lab_referent_can_update_external_request_status(): void
    {
        $labReferent = User::factory()->referent()->create();
        Circle::factory()->create(['slug' => 'lab', 'referent_id' => $labReferent->id]);

        $externalRequest = LabExternalRequest::factory()->citoyen()->create([
            'statut' => LabRequestStatus::Nouvelle,
        ]);

        $this->actingAs($labReferent)
            ->patch(route('lab.external.update-status', $externalRequest), [
                'statut' => LabRequestStatus::EnCours->value,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('lab_external_requests', [
            'id' => $externalRequest->id,
            'statut' => LabRequestStatus::EnCours->value,
        ]);
    }

    public function test_adherent_cannot_update_external_request_status(): void
    {
        $adherent = User::factory()->adherent()->create();
        $externalRequest = LabExternalRequest::factory()->citoyen()->create();

        $this->actingAs($adherent)
            ->patch(route('lab.external.update-status', $externalRequest), [
                'statut' => LabRequestStatus::EnCours->value,
            ])
            ->assertForbidden();
    }

    // -------------------------------------------------------
    // Filtre type sur la liste
    // -------------------------------------------------------

    public function test_list_can_be_filtered_by_type(): void
    {
        $admin = User::factory()->admin()->create();

        LabExternalRequest::factory()->citoyen()->create(['message' => 'Projet citoyen test.']);
        LabExternalRequest::factory()->entreprise()->create(['message' => 'Projet entreprise test.']);

        $this->actingAs($admin)
            ->get(route('lab.external.index', ['type' => 'citoyen']))
            ->assertOk()
            ->assertSee('Projet citoyen test.')
            ->assertDontSee('Projet entreprise test.');
    }
}
