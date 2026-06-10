<h1 align="center">Hop'Initiatives</h1>

<p align="center">
  <strong>La plateforme numérique de l'association — gouvernance partagée, vie associative et accompagnement de projets en un seul espace.</strong>
</p>

<p align="center">
  <a href="#-fonctionnalités">Fonctionnalités</a> ·
  <a href="#-stack-technique">Stack</a> ·
  <a href="#-installation">Installation</a> ·
  <a href="#-déploiement">Déploiement</a> ·
  <a href="#-architecture">Architecture</a>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4-38BDF8?style=flat-square&logo=tailwindcss&logoColor=white" alt="Tailwind 4">
  <img src="https://img.shields.io/badge/Vite-7-646CFF?style=flat-square&logo=vite&logoColor=white" alt="Vite 7">
  <img src="https://img.shields.io/badge/License-MIT-green?style=flat-square" alt="MIT License">
</p>

---

## 📖 À propos

**Hop'Initiatives** est une application web pensée pour la vie d'une association à gouvernance partagée. Elle réunit en un seul endroit l'espace public (vitrine, agenda, consultations citoyennes), l'espace membre organisé en **cercles** de travail, l'accompagnement de projets via **le Lab**, et un back-office d'administration complet.

L'interface est entièrement en français.

---

## ✨ Fonctionnalités

### 🌍 Espace public

- **Vitrine & pages éditoriales** — pages statiques modifiables depuis l'admin
- **Agenda public** des événements + inscription en ligne
- **Inscription / demande d'adhésion** avec validation par l'équipe
- **Chemin de services guidé** — parcours interactif à questions/réponses qui oriente le visiteur vers le bon service
- **Consultations publiques** — sondages citoyens avec résultats et saisie terrain

### 🤝 Le Lab (accompagnement de projets)

- **Demandes externes** — formulaires dédiés citoyens & entreprises (sans authentification)
- **Demandes de soutien interne** pour les membres, avec suivi de statut
- **Catalogue de services** méthodologiques
- **Boîte à outils** — bibliothèque de ressources téléchargeables (liens signés)

### 👥 Espace membre

- **Tableau de bord** personnalisé + onboarding
- **Cercles** — rejoindre/quitter, annuaire, demandes d'adhésion
- **Agenda** de cercle et personnel
- **Journal de bord**, **réunions** & **comptes-rendus**
- **Actions de cercle** (suivi de tâches)
- **Bibliothèque de documents** par cercle
- **Feed** général et par cercle (posts, mise en avant)
- **Sondages** internes et **scrutins formels** (votes officiels)
- **Notifications** in-app

### 🛡️ Rôles & administration

- **Référents de cercle** — validation des demandes, gestion du cercle et de ses documents
- **Admin** — membres (+ export), adhésions, cercles, pages, utilisateurs & promotions, statistiques, parcours guidé, consultations, scrutins, **mode maintenance**

### 🔐 Authentification

- **Lien magique** (magic link) par e-mail
- **Mot de passe** avec configuration post-connexion, réinitialisation
- **Statut de compte** (en attente / actif) via middleware dédié

---

## 🧰 Stack technique

| Domaine | Technologie |
|---|---|
| Framework | **Laravel 12** (PHP 8.2+) |
| Front-end | **Blade**, **Tailwind CSS 4**, **Vite 7** |
| Base de données | SQLite (par défaut) / MySQL / PostgreSQL |
| Outils dev | Pint, Pail, Sail, PHPUnit, Faker |

---

## 🚀 Installation

### Prérequis

- PHP **8.2+**
- Composer
- Node.js & npm

### Démarrage rapide

```bash
# Cloner le dépôt
git clone <url-du-depot> hop-initiatives
cd hop-initiatives

# Installer + configurer en une commande
composer setup
```

Le script `composer setup` installe les dépendances, crée le `.env`, génère la clé d'application, applique les migrations et compile les assets.

### Lancer en développement

```bash
composer dev
```

Cette commande démarre **en parallèle** : le serveur PHP, l'écoute de la file d'attente, les logs (Pail) et Vite.

### Données de test

```bash
php artisan migrate:fresh --seed
```

---

## 📦 Déploiement

Après un `git pull`, lancer depuis la racine du projet :

```bash
./deploy.sh
```

Le script vérifie les prérequis (`php`, `composer`), réinstalle les dépendances si `composer.json` a changé, puis applique les migrations.

---

## 🏗️ Architecture

```text
app/
├── Enums/                 # Énumérations (AccountStatus, …)
├── Http/
│   ├── Controllers/       # Public, Auth, Member, Referent, Admin, Lab
│   └── Middleware/         # admin, referent, account.active, maintenance
├── Models/                # Circle, Event, Consultation, Scrutin, LabService…
├── Mail/                  # E-mails (adhésion, magic link…)
└── Services/              # Logique métier (MembershipApprovalService…)

resources/views/           # Vues Blade (public, member, admin, emails…)
routes/web.php             # Routes groupées par rôle et domaine
database/                  # Migrations, factories, seeders
```

Les routes sont organisées par espace : **public**, **auth**, **membre** (`/mon-espace`), **référent** (`/referent`), **Lab** (`/lab`) et **admin** (`/admin`), chacun protégé par les middlewares appropriés.

---

## 🧪 Tests

```bash
composer test
```

---

## 📄 Licence

Distribué sous licence **MIT**.
