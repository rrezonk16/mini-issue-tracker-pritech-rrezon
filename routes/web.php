<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::get('/', [ProjectController::class, 'index'])->middleware('auth')->name('home');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/owned', [ProjectController::class, 'owned'])->name('projects.owned');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::get('/projects/{project}/issues/create', [IssueController::class, 'create'])->name('issues.create');
    Route::post('/projects/{project}/issues', [IssueController::class, 'store'])->name('issues.store');
    Route::get('/projects/{project}/issues/{issue}', [IssueController::class, 'show'])->name('issues.show');
    Route::get('/projects/{project}/issues/{issue}/edit', [IssueController::class, 'edit'])->name('issues.edit');
    Route::put('/projects/{project}/issues/{issue}', [IssueController::class, 'update'])->name('issues.update');
    Route::delete('/projects/{project}/issues/{issue}', [IssueController::class, 'destroy'])->name('issues.destroy');
});
