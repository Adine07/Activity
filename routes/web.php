<?php

Route::get('/', function () {
    return to_route('login');
})->name('index');

Route::livewire('/login', 'pages::login')->name('login');

Route::get('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
});

Route::livewire('/register', 'pages::register');
Route::livewire('/forgot-password', 'pages::forgot-password')->name('password.request');
Route::livewire('/reset-password/{token}', 'pages::reset-password')->name('password.reset');

Route::middleware('auth')->group(function () {
    Route::livewire('/dashboard', 'pages::index')->name('dashboard');
    Route::livewire('/profile', 'pages::profile')->name('profile');

    Route::name('users.')->group(function () {
        Route::livewire('/users', 'pages::users.index')->name('index');
        Route::livewire('/users/create', 'pages::users.create')->name('create');
        Route::livewire('/users/{user}/edit', 'pages::users.edit')->name('edit');
    });

    Route::name('activity.')->group(function () {
        Route::livewire('/activity', 'pages::activity.index')->name('index');
        Route::livewire('/activity/create', 'pages::activity.create')->name('create');
        Route::livewire('/activity/{activity}/edit', 'pages::activity.edit')->name('edit');
    });

    Route::name('projects.')->group(function () {
        Route::livewire('/projects', 'pages::projects.index')->name('index');
        Route::livewire('/projects/create', 'pages::projects.create')->name('create');
        Route::livewire('/projects/{project}/edit', 'pages::projects.edit')->name('edit');
    });
});
