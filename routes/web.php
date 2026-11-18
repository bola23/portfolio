<?php

use App\Http\Controllers\Site\ContactController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\ProjectController;
use App\Http\Controllers\Site\SeoController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{slug}', [ProjectController::class, 'show'])
    ->where('slug', '[a-z0-9-]+')
    ->name('projects.show');

Route::post('/contact', ContactController::class)
    ->middleware('throttle:contact')
    ->name('contact.store');

Route::get('/cv', [SeoController::class, 'cv'])->name('cv');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
