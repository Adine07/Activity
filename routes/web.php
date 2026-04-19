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

    Route::name('users.')->group(function () {
        Route::livewire('/users', 'pages::users.index')->name('index');
        Route::livewire('/users/create', 'pages::users.create')->name('create');
        Route::livewire('/users/{user}/edit', 'pages::users.edit')->name('edit');
    });

    Route::name('timesheet.')->group(function () {
        Route::livewire('/timesheet', 'pages::timesheet.index')->name('index');
        Route::livewire('/timesheet/create', 'pages::timesheet.create')->name('create');
        Route::livewire('/timesheet/{timesheet}/edit', 'pages::timesheet.edit')->name('edit');
    });

    Route::name('projects.')->group(function () {
        Route::livewire('/projects', 'pages::projects.index')->name('index');
        Route::livewire('/projects/create', 'pages::projects.create')->name('create');
        Route::livewire('/projects/{project}/edit', 'pages::projects.edit')->name('edit');
    });
});
