<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// 18Routes accessibles uniquement aux utilisateurs non authentifiés
Route::middleware('guest')->group(function () {
    // 21-Affiche le formulaire d'inscription
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    // 22-Traite la soumission du formulaire d'inscription
    Route::post('register', [RegisteredUserController::class, 'store']);

    // 23-Affiche le formulaire de connexion
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    // 24-Traite la soumission du formulaire de connexion
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // 26-Affiche le formulaire de demande de réinitialisation de mot de passe
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    // Envoie un email pour réinitialiser le mot de passe
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    // Affiche le formulaire de réinitialisation de mot de passe
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    // Traite la soumission du formulaire de réinitialisation de mot de passe
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

// Routes accessibles uniquement aux utilisateurs authentifiés
Route::middleware('auth')->group(function () {
    // Affiche une notification demandant de vérifier l'email
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    // Vérifie l'email de l'utilisateur via un lien signé
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Envoie une nouvelle notification de vérification d'email
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Affiche le formulaire de confirmation du mot de passe
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    // Traite la soumission du formulaire de confirmation du mot de passe
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    // Déconnecte l'utilisateur
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
