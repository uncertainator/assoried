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
use App\Http\Controllers\MagicLinkController;
use App\Http\Controllers\Member\AccountController;
use App\Http\Controllers\Member\CircleController;
use App\Http\Controllers\Member\DashboardController;
use App\Http\Controllers\Member\NotificationController;
use App\Http\Controllers\Member\PasswordController;
use App\Http\Controllers\Referent\CircleRequestController as ReferentCircleRequestController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

/* ============================================================
   Public routes
   ============================================================ */
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/evenements', fn () => view('coming-soon', ['title' => 'Événements', 'soon' => 'L\'agenda des événements']))->name('evenements');
Route::get('/mentions-legales', fn () => view('legal.mentions'))->name('legal.mentions');
Route::get('/politique-confidentialite', fn () => view('legal.privacy'))->name('legal.privacy');

Route::get('/inscription', [RegistrationController::class, 'show'])->name('inscription');
Route::post('/inscription', [RegistrationController::class, 'store'])->name('inscription.store');

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
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('/mon-profil', fn () => view('member.profile'))->name('profile');
    Route::post('/mot-de-passe', [PasswordController::class, 'update'])->name('password.update');
    Route::delete('/mon-compte', [AccountController::class, 'destroy'])->name('account.destroy');
});

/* ============================================================
   Referent routes
   ============================================================ */
Route::middleware(['auth', 'referent'])->prefix('referent')->name('referent.')->group(function () {
    Route::get('/demandes', [ReferentCircleRequestController::class, 'index'])->name('requests.index');
    Route::post('/demandes/{membership}/approuver', [ReferentCircleRequestController::class, 'approve'])->name('requests.approve');
    Route::post('/demandes/{membership}/refuser', [ReferentCircleRequestController::class, 'reject'])->name('requests.reject');
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
