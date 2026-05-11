# Plan du projet — La Fabrique Benfeld

## Architecture des rôles

Le système de rôles repose sur un backed enum PHP `UserRole` (`admin`, `referent`, `adherent`) casté sur le modèle `User`.
Un référent est associé à un seul cercle via `circles.referent_id` (FK nullable, nullOnDelete).

## Vagues de développement

### Vague 1 — Authentification & cercles (livré)
- Auth hybride (magic link + mot de passe)
- Gestion des cercles (CRUD admin)
- Espace membre : liste des cercles, demande d'adhésion

### Vague 1 bis — Rôles & Référents (livré)
- Enum `UserRole`, migration `role` sur `users`, migration `referent_id` sur `circles`
- Promotion / rétrogradation d'un adhérent en référent (interface admin)
- Espace référent : édition du cercle assigné, gestion des demandes d'inscription
- Policies : `UserRolePolicy`, `CirclePolicy`, `CircleMembershipPolicy`

### US-B — Validation des inscriptions (livré)
- Validation / rejet des demandes d'adhésion par le référent ou l'admin
- Exclusion d'un membre du cercle
- Notifications par email des décisions

### Vague 2 — Journal de bord (à venir)

#### Anticipation : autorisation de publication
La future `CirclePostPolicy` (Vague 2) doit autoriser la publication si `$circle->isManagedBy($user)`.
Cette méthode est déjà disponible sur le modèle `Circle` et retourne `true` si l'utilisateur est admin
ou est le référent assigné au cercle.

La `CircleMembershipPolicy` expose déjà un stub `exclude()` utilisable pour l'exclusion de membres
dans le journal de bord si nécessaire.

#### Fonctionnalités prévues
- Publication d'articles / annonces dans le journal de bord d'un cercle
- Visibilité : membres du cercle uniquement, ou publique
- Modération par le référent ou l'admin

## Conventions

- PSR-12 + Laravel Pint
- Contrôleurs minces (logique dans models / services)
- FormRequests pour toute validation entrante
- Routes nommées, préfixes : `member.`, `referent.`, `admin.`
- Policies enregistrées dans `AppServiceProvider`
- Tests Pest, `RefreshDatabase`
