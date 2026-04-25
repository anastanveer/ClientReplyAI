<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/pricing', 'pages.pricing')->name('pricing');
Route::view('/offline', 'offline')->name('offline');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function (): void {
    Route::view('chat-history', 'pages.chat-history')->name('chat-history');
    Route::view('saved-replies', 'pages.saved-replies')->name('saved-replies');
    Route::view('templates', 'pages.templates')->name('templates');
    Route::view('settings', 'pages.settings')->name('settings');
    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
