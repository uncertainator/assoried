<?php

use App\Http\Controllers\Admin\CircleController as AdminCircleController;
use App\Http\Controllers\Admin\CircleRequestController as AdminCircleRequestController;
use App\Http\Controllers\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\PasswordLoginController;
use App\Http\Controllers\Auth\PasswordSetupController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LabExternalRequestController;
use App\Http\Controllers\LabInternalRequestController;
use App\Http\Controllers\LabServiceController;
use App\Http\Controllers\MagicLinkController;
use App\Http\Controllers\Member\AccountController;
use App\Http\Controllers\Member\CircleActionController;
use App\Http\Controllers\Member\CircleController;
use App\Http\Controllers\Member\CircleDocumentController as MemberCircleDocumentController;
use App\Http\Controllers\Member\CircleJournalEntryController;
use App\Http\Controllers\Member\DashboardController;
use App\Http\Controllers\Member\EventController;
use App\Http\Controllers\Member\GeneralFeedController;
use App\Http\Controllers\Member\MeetingController;
use App\Http\Controllers\Member\MeetingReportController;
use App\Http\Controllers\Member\NotificationController;
use App\Http\Controllers\Member\PasswordController;
use App\Http\Controllers\Member\PostController;
use App\Http\Controllers\PublicAgendaController;
use App\Http\Controllers\Referent\CircleController as ReferentCircleController;
use App\Http\Controllers\Referent\CircleDocumentController as ReferentCircleDocumentController;
use App\Http\Controllers\Referent\CircleRequestController as ReferentCircleRequestController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

/* ============================================================
   Public routes
   ============================================================ */
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/agenda-public', [PublicAgendaController::class, 'index'])->name('public.agenda');
Route::get('/evenements', fn () => view('coming-soon', ['title' => 'Événements', 'soon' => 'L\'agenda des événements']))->name('evenements');
Route::get('/mentions-legales', fn () => view('legal.mentions'))->name('legal.mentions');
Route::get('/politique-confidentialite', fn () => view('legal.privacy'))->name('legal.privacy');

Route::get('/inscription', [RegistrationController::class, 'show'])->name('inscription');
Route::post('/inscription', [RegistrationController::class, 'store'])->name('inscription.store');

/* ============================================================
   Lab — demandes publiques externes (sans authentification)
   ============================================================ */
Route::get('/lab/citoyen', [LabExternalRequestController::class, 'showCitoyen'])->name('lab.external.citoyen');
Route::post('/lab/citoyen', [LabExternalRequestController::class, 'storeCitoyen'])
    ->middleware('throttle:10,1')
    ->name('lab.external.citoyen.store');

Route::get('/lab/entreprise', [LabExternalRequestController::class, 'showEntreprise'])->name('lab.external.entreprise');
Route::post('/lab/entreprise', [LabExternalRequestController::class, 'storeEntreprise'])
    ->middleware('throttle:10,1')
    ->name('lab.external.entreprise.store');

Route::get('/lab/demande-recue', fn () => view('lab.external.confirmation'))->name('lab.external.confirmation');

/* ============================================================
   Auth — connexion (magic link + mot de passe)
   ============================================================ */
Route::get('/connexion', [MagicLinkController::class, 'showForm'])->name('login');
Route::post('/connexion', [PasswordLoginController::class, 'store'])->name('login.password');
Route::post('/auth/magic-link', [MagicLinkController::class, 'send'])->name('auth.magic.send');
Route::get('/auth/magic-link/verify', [MagicLinkController::class, 'verify'])->name('auth.magic.verify')->middleware('signed');
Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');

Route::get('/lien-envoye', fn () => view('auth.link-sent'))->name('auth.link-sent');
Route::get('/lien-invalide', fn () => view('auth.link-invalid'))->name('auth.link-invalid');

/* ============================================================
   Auth — mot de passe oublié / réinitialisation
   ============================================================ */
Route::get('/mot-de-passe-oublie', [ForgotPasswordController::class, 'show'])->name('password.request');
Route::post('/mot-de-passe-oublie', [ForgotPasswordController::class, 'store'])->name('password.email');
Route::get('/reinitialisation-mot-de-passe/{token}', [ResetPasswordController::class, 'show'])->name('password.reset');
Route::post('/reinitialisation-mot-de-passe', [ResetPasswordController::class, 'store'])->name('password.update');

/* ============================================================
   Member routes (authenticated)
   ============================================================ */
Route::middleware('auth')->prefix('mon-espace')->name('member.')->group(function () {
    Route::get('/tableau-de-bord', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/cercles', [CircleController::class, 'index'])->name('circles.index');
    Route::post('/cercles/{circle}/rejoindre', [CircleController::class, 'join'])->name('circles.join');
    Route::delete('/cercles/{circle}/quitter', [CircleController::class, 'leave'])->name('circles.leave');
    Route::delete('/cercles/{circle}/annuler-demande', [CircleController::class, 'cancelRequest'])->name('circles.cancel');
    Route::get('/cercles/{circle}/annuaire', [CircleController::class, 'directory'])->name('circles.directory');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('/mon-profil', fn () => view('member.profile'))->name('profile');
    Route::post('/mot-de-passe', [PasswordController::class, 'update'])->name('password.update');
    Route::delete('/mon-compte', [AccountController::class, 'destroy'])->name('account.destroy');

    // Agenda
    Route::get('/agenda', [EventController::class, 'index'])->name('agenda.index');
    Route::get('/cercles/{circle}/agenda', [EventController::class, 'circleIndex'])->name('circles.agenda');
    Route::get('/cercles/{circle}/agenda/creer', [EventController::class, 'create'])->name('agenda.create');
    Route::post('/cercles/{circle}/agenda', [EventController::class, 'store'])->name('agenda.store');
    Route::get('/agenda/{event}/modifier', [EventController::class, 'edit'])->name('agenda.edit');
    Route::put('/agenda/{event}', [EventController::class, 'update'])->name('agenda.update');
    Route::delete('/agenda/{event}', [EventController::class, 'destroy'])->name('agenda.destroy');

    // Journal de bord
    Route::get('/cercles/{circle}/journal', [CircleJournalEntryController::class, 'index'])->name('circles.journal.index');
    Route::get('/cercles/{circle}/journal/creer', [CircleJournalEntryController::class, 'create'])->name('circles.journal.create');
    Route::post('/cercles/{circle}/journal', [CircleJournalEntryController::class, 'store'])->name('circles.journal.store');
    Route::get('/cercles/{circle}/journal/{entry}/modifier', [CircleJournalEntryController::class, 'edit'])->name('circles.journal.edit');
    Route::put('/cercles/{circle}/journal/{entry}', [CircleJournalEntryController::class, 'update'])->name('circles.journal.update');
    Route::delete('/cercles/{circle}/journal/{entry}', [CircleJournalEntryController::class, 'destroy'])->name('circles.journal.destroy');

    // Réunions
    Route::get('/cercles/{circle}/reunions', [MeetingController::class, 'index'])->name('circles.meetings.index');
    Route::get('/cercles/{circle}/reunions/creer', [MeetingController::class, 'create'])->name('meetings.create');
    Route::post('/cercles/{circle}/reunions', [MeetingController::class, 'store'])->name('meetings.store');
    Route::get('/reunions/{meeting}', [MeetingController::class, 'show'])->name('meetings.show');

    // Comptes-rendus de réunion
    Route::get('/reunions/{meeting}/compte-rendus/creer', [MeetingReportController::class, 'create'])->name('meeting-reports.create');
    Route::post('/reunions/{meeting}/compte-rendus', [MeetingReportController::class, 'store'])->name('meeting-reports.store');
    Route::get('/compte-rendus/{report}', [MeetingReportController::class, 'show'])->name('meeting-reports.show');
    Route::get('/compte-rendus/{report}/modifier', [MeetingReportController::class, 'edit'])->name('meeting-reports.edit');
    Route::put('/compte-rendus/{report}', [MeetingReportController::class, 'update'])->name('meeting-reports.update');
    Route::post('/compte-rendus/{report}/publier', [MeetingReportController::class, 'publish'])->name('meeting-reports.publish');

    // Actions de cercle
    Route::post('/cercles/{circle}/actions', [CircleActionController::class, 'store'])->name('circle.actions.store');
    Route::patch('/actions/{action}', [CircleActionController::class, 'update'])->name('circle.actions.update');
    Route::delete('/actions/{action}', [CircleActionController::class, 'destroy'])->name('circle.actions.destroy');

    // Bibliothèque de documents
    Route::get('/cercles/{circle}/documents', [MemberCircleDocumentController::class, 'index'])->name('circles.documents.index');

    // Feed cercle + feed général
    Route::get('/cercles/{circle}', [PostController::class, 'index'])->name('circles.show');
    Route::post('/cercles/{circle}/posts', [PostController::class, 'store'])->name('circles.posts.store');
    Route::patch('/posts/{post}/pousser', [PostController::class, 'pushToGeneral'])->name('posts.push');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::get('/feed', [GeneralFeedController::class, 'index'])->name('feed');
});

/* ============================================================
   Referent routes
   ============================================================ */
Route::middleware(['auth', 'referent'])->prefix('referent')->name('referent.')->group(function () {
    Route::get('/demandes', [ReferentCircleRequestController::class, 'index'])->name('requests.index');
    Route::post('/demandes/{membership}/approuver', [ReferentCircleRequestController::class, 'approve'])->name('requests.approve');
    Route::post('/demandes/{membership}/refuser', [ReferentCircleRequestController::class, 'reject'])->name('requests.reject');
    Route::get('/circle/edit', [ReferentCircleController::class, 'edit'])->name('circle.edit');
    Route::put('/circle', [ReferentCircleController::class, 'update'])->name('circle.update');

    // Bibliothèque de documents
    Route::get('/circle/{circle}/documents/creer', [ReferentCircleDocumentController::class, 'create'])->name('circle.documents.create');
    Route::post('/circle/{circle}/documents', [ReferentCircleDocumentController::class, 'store'])->name('circle.documents.store');
    Route::delete('/circle/{circle}/documents/{document}', [ReferentCircleDocumentController::class, 'destroy'])->name('circle.documents.destroy');
});

/* ============================================================
   Lab — catalogue de services (tout membre connecté)
   ============================================================ */
Route::middleware('auth')->prefix('lab/services')->name('lab.services.')->group(function () {
    Route::get('/', [LabServiceController::class, 'index'])->name('index');
    Route::get('/create', [LabServiceController::class, 'create'])->name('create');
    Route::post('/', [LabServiceController::class, 'store'])->name('store');
    Route::get('/{service}', [LabServiceController::class, 'show'])->name('show');
    Route::get('/{service}/edit', [LabServiceController::class, 'edit'])->name('edit');
    Route::put('/{service}', [LabServiceController::class, 'update'])->name('update');
    Route::delete('/{service}', [LabServiceController::class, 'destroy'])->name('destroy');
});

/* ============================================================
   Lab — liste demandes externes (référents Lab + admins)
   ============================================================ */
Route::middleware('auth')->group(function () {
    Route::get('/lab/external-requests', [LabExternalRequestController::class, 'index'])->name('lab.external.index');
    Route::patch('/lab/external-requests/{labExternalRequest}/statut', [LabExternalRequestController::class, 'updateStatus'])->name('lab.external.update-status');
});

/* ============================================================
   Lab — demandes de soutien interne (tout membre connecté)
   ============================================================ */
Route::middleware('auth')->prefix('lab')->name('lab.')->group(function () {
    Route::get('/demandes/nouvelle', [LabInternalRequestController::class, 'create'])->name('requests.create');
    Route::post('/demandes', [LabInternalRequestController::class, 'store'])->name('requests.store');
    Route::get('/mes-demandes', [LabInternalRequestController::class, 'myRequests'])->name('requests.my');
    Route::get('/demandes', [LabInternalRequestController::class, 'index'])->name('requests.index');
    Route::patch('/demandes/{labInternalRequest}/statut', [LabInternalRequestController::class, 'updateStatus'])->name('requests.update-status');
});

/* ============================================================
   Auth — setup mot de passe post magic link (protégé)
   ============================================================ */
Route::middleware('auth')->group(function () {
    Route::get('/mon-compte/mot-de-passe/configurer', [PasswordSetupController::class, 'show'])->name('account.password.setup');
    Route::post('/mon-compte/mot-de-passe/configurer', [PasswordSetupController::class, 'store'])->name('account.password.store');
    Route::post('/mon-compte/mot-de-passe/ignorer', [PasswordSetupController::class, 'dismiss'])->name('account.password.dismiss');
});

/* ============================================================
   Admin routes
   ============================================================ */
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.members.index'))->name('index');
    Route::get('/membres', [AdminMemberController::class, 'index'])->name('members.index');
    Route::get('/membres/export', [AdminMemberController::class, 'export'])->name('members.export');
    Route::get('/demandes', [AdminCircleRequestController::class, 'index'])->name('requests.index');
    Route::post('/demandes/{membership}/approuver', [AdminCircleRequestController::class, 'approve'])->name('requests.approve');
    Route::post('/demandes/{membership}/refuser', [AdminCircleRequestController::class, 'reject'])->name('requests.reject');
    Route::resource('/cercles', AdminCircleController::class)->except(['show', 'destroy'])
        ->parameters(['cercles' => 'circle'])
        ->names([
            'index' => 'circles.index',
            'create' => 'circles.create',
            'store' => 'circles.store',
            'edit' => 'circles.edit',
            'update' => 'circles.update',
        ]);

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/promote', [AdminUserController::class, 'promoteForm'])->name('users.promote.form');
    Route::post('/users/{user}/promote', [AdminUserController::class, 'promote'])->name('users.promote');
    Route::post('/users/{user}/demote', [AdminUserController::class, 'demote'])->name('users.demote');
});
