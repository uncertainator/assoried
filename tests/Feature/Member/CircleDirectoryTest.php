<?php

namespace Tests\Feature\Member;

use App\Models\Circle;
use App\Models\CircleMembership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CircleDirectoryTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    private function makeApprovedMember(Circle $circle, array $userAttributes = []): User
    {
        $user = User::factory()->adherent()->create($userAttributes);
        CircleMembership::factory()->approved()->create([
            'user_id' => $user->id,
            'circle_id' => $circle->id,
        ]);

        return $user;
    }

    // -----------------------------------------------------------------------
    // Contrôle d'accès
    // -----------------------------------------------------------------------

    public function test_invite_redirige_vers_login(): void
    {
        $circle = Circle::factory()->create();

        $this->get(route('member.circles.directory', $circle))
            ->assertRedirect(route('login'));
    }

    public function test_adherent_sans_membership_recoit_403(): void
    {
        $user = User::factory()->adherent()->create();
        $circle = Circle::factory()->create();

        $this->actingAs($user)
            ->get(route('member.circles.directory', $circle))
            ->assertForbidden();
    }

    public function test_adherent_avec_membership_pending_recoit_403(): void
    {
        $user = User::factory()->adherent()->create();
        $circle = Circle::factory()->create();
        CircleMembership::factory()->pending()->create([
            'user_id' => $user->id,
            'circle_id' => $circle->id,
        ]);

        $this->actingAs($user)
            ->get(route('member.circles.directory', $circle))
            ->assertForbidden();
    }

    public function test_adherent_avec_membership_rejected_recoit_403(): void
    {
        $user = User::factory()->adherent()->create();
        $circle = Circle::factory()->create();
        CircleMembership::factory()->rejected()->create([
            'user_id' => $user->id,
            'circle_id' => $circle->id,
        ]);

        $this->actingAs($user)
            ->get(route('member.circles.directory', $circle))
            ->assertForbidden();
    }

    public function test_adherent_avec_membership_approved_peut_acceder(): void
    {
        $circle = Circle::factory()->create();
        $user = $this->makeApprovedMember($circle);

        $this->actingAs($user)
            ->get(route('member.circles.directory', $circle))
            ->assertOk();
    }

    public function test_admin_sans_membership_peut_acceder(): void
    {
        $admin = User::factory()->admin()->create();
        $circle = Circle::factory()->create();

        $this->actingAs($admin)
            ->get(route('member.circles.directory', $circle))
            ->assertOk();
    }

    public function test_referent_du_cercle_peut_acceder_sans_ligne_pivot(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);

        $this->actingAs($referent)
            ->get(route('member.circles.directory', $circle))
            ->assertOk();
    }

    // -----------------------------------------------------------------------
    // Consentement contact
    // -----------------------------------------------------------------------

    public function test_email_visible_si_consentement_true(): void
    {
        $circle = Circle::factory()->create();
        $member = $this->makeApprovedMember($circle, ['consent_display_contact' => true]);

        $this->actingAs($member)
            ->get(route('member.circles.directory', $circle))
            ->assertSee($member->email);
    }

    public function test_email_cache_si_consentement_false(): void
    {
        $circle = Circle::factory()->create();
        $member = $this->makeApprovedMember($circle, ['consent_display_contact' => false]);

        $this->actingAs($member)
            ->get(route('member.circles.directory', $circle))
            ->assertDontSee($member->email);
    }

    // -----------------------------------------------------------------------
    // Visibilité des rôles
    // -----------------------------------------------------------------------

    public function test_admin_voit_les_badges_de_role(): void
    {
        $admin = User::factory()->admin()->create();
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $this->makeApprovedMember($circle);

        $this->actingAs($admin)
            ->get(route('member.circles.directory', $circle))
            ->assertSee('Référent')
            ->assertSee('Adhérent');
    }

    public function test_referent_voit_les_badges_de_role(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $this->makeApprovedMember($circle);

        $this->actingAs($referent)
            ->get(route('member.circles.directory', $circle))
            ->assertSee('Référent')
            ->assertSee('Adhérent');
    }

    public function test_adherent_approuve_ne_voit_pas_les_badges_de_role(): void
    {
        $referent = User::factory()->referent()->create();
        $circle = Circle::factory()->create(['referent_id' => $referent->id]);
        $visitor = $this->makeApprovedMember($circle);

        $this->actingAs($visitor)
            ->get(route('member.circles.directory', $circle))
            ->assertDontSee('Référent')
            ->assertDontSee('Adhérent');
    }

    // -----------------------------------------------------------------------
    // Compteur de membres
    // -----------------------------------------------------------------------

    public function test_admin_voit_le_compteur_de_membres(): void
    {
        $admin = User::factory()->admin()->create();
        $circle = Circle::factory()->create();
        $this->makeApprovedMember($circle);
        $this->makeApprovedMember($circle);
        $this->makeApprovedMember($circle);

        $this->actingAs($admin)
            ->get(route('member.circles.directory', $circle))
            ->assertSee('3 membres');
    }

    public function test_adherent_ne_voit_pas_le_compteur_de_membres(): void
    {
        $circle = Circle::factory()->create();
        $this->makeApprovedMember($circle);
        $this->makeApprovedMember($circle);
        $visitor = $this->makeApprovedMember($circle);

        $this->actingAs($visitor)
            ->get(route('member.circles.directory', $circle))
            ->assertDontSee('3 membres');
    }

    // -----------------------------------------------------------------------
    // Tri alphabétique
    // -----------------------------------------------------------------------

    public function test_membres_affiches_par_ordre_alphabetique(): void
    {
        $circle = Circle::factory()->create();
        $this->makeApprovedMember($circle, ['name' => 'Zelda Martin']);
        $this->makeApprovedMember($circle, ['name' => 'Alice Dupont']);
        $this->makeApprovedMember($circle, ['name' => 'Marc Bernard']);

        $visitor = $this->makeApprovedMember($circle);

        $this->actingAs($visitor)
            ->get(route('member.circles.directory', $circle))
            ->assertSeeInOrder(['Alice Dupont', 'Marc Bernard', 'Zelda Martin']);
    }
}
