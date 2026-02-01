<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\ProjectController::class, 'landing'])->name('landing');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('auth');

// Public / Shared Routes
Route::get('/projects', [App\Http\Controllers\ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{project}', [App\Http\Controllers\ProjectController::class, 'show'])->name('projects.show');

// Entrepreneur Routes
Route::middleware(['auth', 'role:entrepreneur'])->group(function () {
    Route::get('/my-projects', [App\Http\Controllers\ProjectController::class, 'myProjects'])->name('projects.my');
    Route::get('/projects/create/new', [App\Http\Controllers\ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [App\Http\Controllers\ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [App\Http\Controllers\ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [App\Http\Controllers\ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [App\Http\Controllers\ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::post('/updates', [App\Http\Controllers\UpdateController::class, 'store'])->name('updates.store');
});

// Investor Routes
Route::middleware(['auth', 'role:investor'])->group(function () {
    Route::get('/wallet', function () {
        return view('wallet');
    })->name('wallet.show');
    Route::post('/wallet/add', [App\Http\Controllers\InvestmentController::class, 'addFunds'])->name('wallet.add');
    Route::post('/investments', [App\Http\Controllers\InvestmentController::class, 'store'])->name('investments.store');
    Route::delete('/investments/{investment}', [App\Http\Controllers\InvestmentController::class, 'destroy'])->name('investments.destroy');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
    Route::patch('/users/{user}/ban', [App\Http\Controllers\AdminController::class, 'toggleBan'])->name('admin.users.ban');
    Route::get('/projects', [App\Http\Controllers\AdminController::class, 'projects'])->name('admin.projects');
    Route::post('/projects/{project}/review', [App\Http\Controllers\AdminController::class, 'reviewProject'])->name('admin.projects.review');
});
