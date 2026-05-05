<?php

use App\Http\Controllers\Admin\CircleController as AdminCircleController;
use App\Http\Controllers\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MagicLinkController;
use App\Http\Controllers\Member\AccountController;
use App\Http\Controllers\Member\CircleController;
use App\Http\Controllers\Member\DashboardController;
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
   Auth — magic link
   ============================================================ */
Route::get('/connexion', [MagicLinkController::class, 'showForm'])->name('login');
Route::post('/auth/magic-link', [MagicLinkController::class, 'send'])->name('auth.magic.send');
Route::get('/auth/magic-link/verify', [MagicLinkController::class, 'verify'])->name('auth.magic.verify')->middleware('signed');
Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');

Route::get('/lien-envoye', fn () => view('auth.link-sent'))->name('auth.link-sent');
Route::get('/lien-invalide', fn () => view('auth.link-invalid'))->name('auth.link-invalid');

/* ============================================================
   Member routes (authenticated)
   ============================================================ */
Route::middleware('auth')->prefix('mon-espace')->name('member.')->group(function () {
    Route::get('/tableau-de-bord', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/cercles', [CircleController::class, 'index'])->name('circles.index');
    Route::post('/cercles/{circle}/rejoindre', [CircleController::class, 'join'])->name('circles.join');
    Route::delete('/cercles/{circle}/quitter', [CircleController::class, 'leave'])->name('circles.leave');
    Route::get('/mon-profil', fn () => view('member.profile'))->name('profile');
    Route::delete('/mon-compte', [AccountController::class, 'destroy'])->name('account.destroy');
});

/* ============================================================
   Admin routes
   ============================================================ */
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.members.index'))->name('index');
    Route::get('/membres', [AdminMemberController::class, 'index'])->name('members.index');
    Route::get('/membres/export', [AdminMemberController::class, 'export'])->name('members.export');
    Route::resource('/cercles', AdminCircleController::class)->except(['show', 'destroy'])
        ->parameters(['cercles' => 'circle'])
        ->names([
            'index'  => 'circles.index',
            'create' => 'circles.create',
            'store'  => 'circles.store',
            'edit'   => 'circles.edit',
            'update' => 'circles.update',
        ]);
});
