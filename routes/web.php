<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'page'])->name('home');
Route::get('/projects/{slug}', [PublicController::class, 'detail'])->name('projects.show');
Route::get('/articles/{slug}', [PublicController::class, 'detail'])->name('articles.show');
Route::get('/resume/file', [PublicController::class, 'resume'])->name('resume.file');
Route::post('/contact', [PublicController::class, 'contact'])->middleware('throttle:5,10');
Route::post('/chat', [ChatController::class, 'send'])->middleware('throttle:8,1');
Route::post('/chat/feedback', [ChatController::class, 'feedback'])->middleware('throttle:5,1');
Route::get('/sitemap.xml', [PublicController::class, 'sitemap']);
Route::get('/robots.txt', fn () => response("User-agent: *\nAllow: /\nDisallow: /admin\nSitemap: ".url('/sitemap.xml'))->header('Content-Type', 'text/plain'));

Route::prefix('admin')->group(function () {
    Route::get('/login', fn () => view('auth.form', ['mode' => 'login']))->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/forgot-password', fn () => view('auth.form', ['mode' => 'forgot']))->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgot'])->middleware('throttle:3,5')->name('password.email');
    Route::get('/reset-password/{token}', fn (string $token) => view('auth.form', ['mode' => 'reset', 'token' => $token]))->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'reset'])->middleware('throttle:5,1')->name('password.update');
    Route::middleware(['auth', 'can:admin'])->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('admin');
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/settings', [AdminController::class, 'settings']);
        Route::get('/copy', [AdminController::class, 'copy']);
        Route::post('/copy', [AdminController::class, 'saveCopy']);
        Route::post('/settings', [AdminController::class, 'saveSettings']);
        Route::get('/content/{type}', [AdminController::class, 'index']);
        Route::get('/content/{type}/create', [AdminController::class, 'edit']);
        Route::post('/content/{type}', [AdminController::class, 'save']);
        Route::get('/content/{type}/{content}/edit', [AdminController::class, 'edit']);
        Route::put('/content/{type}/{content}', [AdminController::class, 'save']);
        Route::delete('/content/{type}/{content}', [AdminController::class, 'delete']);
        Route::get('/inbox/{kind}', [AdminController::class, 'inbox']);
        Route::post('/inbox/{kind}/{id}', [AdminController::class, 'updateInbox']);
    });
});
Route::get('/{page}', [PublicController::class, 'page'])->whereIn('page', ['about', 'experience', 'skills', 'projects', 'resume', 'contact', 'articles'])->name('page');
