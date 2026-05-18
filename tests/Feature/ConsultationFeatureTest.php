<?php

namespace Tests\Feature;

use App\Enums\ConsultationMode;
use App\Enums\ConsultationSource;
use App\Models\Consultation;
use App\Models\ConsultationReponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultationFeatureTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Soumission publique — avis libre
    // -------------------------------------------------------------------------

    public function test_soumission_avis_libre_valide(): void
    {
        $consultation = Consultation::factory()->ouverte()->create();

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
            ->post(route('consultations.soumettre', $consultation), [
                'contenu' => 'Mon avis test très intéressant.',
            ])
            ->assertRedirect(route('consultations.show', $consultation))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('consultation_reponses', [
            'consultation_id' => $consultation->id,
            'mode' => ConsultationMode::AvisLibre->value,
            'contenu' => 'Mon avis test très intéressant.',
            'source' => ConsultationSource::Numerique->value,
            'masque' => false,
        ]);

        $this->assertDatabaseCount('consultation_reponses', 1);
    }

    public function test_soumission_avis_libre_trop_long_est_rejete(): void
    {
        $consultation = Consultation::factory()->ouverte()->create();

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
            ->post(route('consultations.soumettre', $consultation), [
                'contenu' => str_repeat('a', 501),
            ])
            ->assertSessionHasErrors('contenu');

        $this->assertDatabaseCount('consultation_reponses', 0);
    }

    // -------------------------------------------------------------------------
    // Soumission publique — signature
    // -------------------------------------------------------------------------

    public function test_soumission_signature_valide(): void
    {
        $consultation = Consultation::factory()->signature()->ouverte()->create();

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.2'])
            ->post(route('consultations.soumettre', $consultation), [
                'prenom' => 'Marie',
                'nom' => 'Dupont',
            ])
            ->assertRedirect(route('consultations.show', $consultation));

        $reponse = ConsultationReponse::first();
        $this->assertNotNull($reponse);
        $sig = json_decode($reponse->contenu, true);
        $this->assertEquals('Marie', $sig['prenom']);
        $this->assertEquals('Dupont', $sig['nom']);
    }

    // -------------------------------------------------------------------------
    // Soumission publique — vote indicatif
    // -------------------------------------------------------------------------

    public function test_soumission_vote_indicatif_valide(): void
    {
        $consultation = Consultation::factory()->voteIndicatif()->ouverte()->create();

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.3'])
            ->post(route('consultations.soumettre', $consultation), [
                'choix' => 'Option A',
            ])
            ->assertRedirect(route('consultations.show', $consultation));

        $this->assertDatabaseHas('consultation_reponses', [
            'consultation_id' => $consultation->id,
            'contenu' => 'Option A',
        ]);
    }

    public function test_vote_indicatif_choix_invalide_est_rejete(): void
    {
        $consultation = Consultation::factory()->voteIndicatif()->ouverte()->create();

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.4'])
            ->post(route('consultations.soumettre', $consultation), [
                'choix' => 'Option inexistante',
            ])
            ->assertSessionHasErrors('choix');

        $this->assertDatabaseCount('consultation_reponses', 0);
    }

    // -------------------------------------------------------------------------
    // Anti-spam IP
    // -------------------------------------------------------------------------

    public function test_anti_spam_rejette_quatrieme_soumission_meme_ip(): void
    {
        $consultation = Consultation::factory()->ouverte()->create();

        ConsultationReponse::factory()->count(3)->create([
            'consultation_id' => $consultation->id,
            'ip_address' => '1.2.3.4',
            'source' => ConsultationSource::Numerique->value,
        ]);

        $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])
            ->post(route('consultations.soumettre', $consultation), [
                'contenu' => 'Quatrième tentative.',
            ])
            ->assertSessionHasErrors('ip');

        $this->assertDatabaseCount('consultation_reponses', 3);
    }

    public function test_anti_spam_autorise_apres_24h(): void
    {
        $consultation = Consultation::factory()->ouverte()->create();

        ConsultationReponse::factory()->count(3)->create([
            'consultation_id' => $consultation->id,
            'ip_address' => '1.2.3.4',
            'source' => ConsultationSource::Numerique->value,
            'created_at' => now()->subHours(25),
        ]);

        $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])
            ->post(route('consultations.soumettre', $consultation), [
                'contenu' => 'Nouvelle réponse après 24h.',
            ])
            ->assertRedirect(route('consultations.show', $consultation));

        $this->assertDatabaseCount('consultation_reponses', 4);
    }

    public function test_soumission_bloquee_si_consultation_cloturee(): void
    {
        $consultation = Consultation::factory()->cloturee()->create();

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.5'])
            ->post(route('consultations.soumettre', $consultation), [
                'contenu' => 'Tentative après clôture.',
            ])
            ->assertSessionHasErrors('consultation');

        $this->assertDatabaseCount('consultation_reponses', 0);
    }

    // -------------------------------------------------------------------------
    // Admin — saisie terrain
    // -------------------------------------------------------------------------

    public function test_admin_saisie_terrain_sans_restriction_ip(): void
    {
        $admin = User::factory()->admin()->create();
        $consultation = Consultation::factory()->ouverte()->create();

        ConsultationReponse::factory()->count(3)->create([
            'consultation_id' => $consultation->id,
            'ip_address' => '127.0.0.1',
            'source' => ConsultationSource::Numerique->value,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.consultations.terrain.store', $consultation), [
                'reponses' => [
                    ['contenu' => 'Avis terrain A'],
                    ['contenu' => 'Avis terrain B'],
                ],
            ])
            ->assertRedirect(route('admin.consultations.show', $consultation));

        $this->assertDatabaseHas('consultation_reponses', [
            'source' => ConsultationSource::Terrain->value,
            'ip_address' => null,
        ]);

        $this->assertDatabaseCount('consultation_reponses', 5);
    }

    // -------------------------------------------------------------------------
    // Admin — modération
    // -------------------------------------------------------------------------

    public function test_admin_peut_masquer_un_avis(): void
    {
        $admin = User::factory()->admin()->create();
        $consultation = Consultation::factory()->ouverte()->create();
        $reponse = ConsultationReponse::factory()->create([
            'consultation_id' => $consultation->id,
            'masque' => false,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.consultations.reponses.masquer', $reponse))
            ->assertRedirect();

        $this->assertDatabaseHas('consultation_reponses', [
            'id' => $reponse->id,
            'masque' => true,
        ]);
    }

    public function test_admin_peut_demasquer_un_avis(): void
    {
        $admin = User::factory()->admin()->create();
        $consultation = Consultation::factory()->ouverte()->create();
        $reponse = ConsultationReponse::factory()->masquee()->create([
            'consultation_id' => $consultation->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.consultations.reponses.demasquer', $reponse))
            ->assertRedirect();

        $this->assertDatabaseHas('consultation_reponses', [
            'id' => $reponse->id,
            'masque' => false,
        ]);
    }

    public function test_avis_masque_absent_de_la_vue_publique(): void
    {
        $consultation = Consultation::factory()->ouverte()->create();

        $reponseVisible = ConsultationReponse::factory()->create([
            'consultation_id' => $consultation->id,
            'contenu' => 'Avis public visible',
            'masque' => false,
        ]);

        $reponseMasquee = ConsultationReponse::factory()->masquee()->create([
            'consultation_id' => $consultation->id,
            'contenu' => 'Avis confidentiel masqué',
        ]);

        $this->get(route('consultations.resultats', $consultation))
            ->assertOk()
            ->assertSee($reponseVisible->contenu)
            ->assertDontSee($reponseMasquee->contenu);
    }

    // -------------------------------------------------------------------------
    // Sécurité — IP jamais exposée
    // -------------------------------------------------------------------------

    public function test_ip_non_exposee_dans_resultats_publics(): void
    {
        $consultation = Consultation::factory()->ouverte()->create();

        ConsultationReponse::factory()->create([
            'consultation_id' => $consultation->id,
            'ip_address' => '192.168.1.99',
            'contenu' => 'Avis avec IP',
            'masque' => false,
        ]);

        $this->get(route('consultations.resultats', $consultation))
            ->assertOk()
            ->assertDontSee('192.168.1.99');
    }
}
