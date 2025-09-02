<?php

use App\Http\Controllers\CommentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


use App\Http\Controllers\IssueController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TagController;

Route::prefix('projects')->middleware('auth:api')->group(function () {
    Route::get('/', [ProjectController::class, 'apiIndex'])->name('api.projects.index');
    Route::post('/', [ProjectController::class, 'apiStore'])->name('api.projects.store');
    Route::get('/{project}', [ProjectController::class, 'apiShow'])->name('api.projects.show');
    Route::put('/{project}', [ProjectController::class, 'apiUpdate'])->name('api.projects.update');
    Route::delete('/{project}', [ProjectController::class, 'apiDestroy'])->name('api.projects.destroy');

    Route::prefix('{project}/issues')->group(function () {
        Route::get('/', [IssueController::class, 'apiIndex'])->name('api.issues.index');
        Route::post('/', [IssueController::class, 'apiStore'])->name('api.issues.store');
        Route::get('/{issue}', [IssueController::class, 'apiShow'])->name('api.issues.show');
        Route::put('/{issue}', [IssueController::class, 'apiUpdate'])->name('api.issues.update');
        Route::delete('/{issue}', [IssueController::class, 'apiDestroy'])->name('api.issues.destroy');
    });
});

Route::prefix('projects/{project}')->group(function () {
    Route::get('/issues/{issue}', [IssueController::class, 'apiShow'])->name('api.issues.show');
});                     
Route::get('/projects/{project}/issues/{issue}/comments', [CommentController::class, 'index'])->name('api.comments.index');
Route::post('/projects/{project}/issues/{issue}/comments', [CommentController::class, 'store'])->name('api.comments.store');

Route::get('/tags', [TagController::class, 'index'])->name('api.tags.index');
Route::post('/tags', [TagController::class, 'store'])->name('api.tags.store');
Route::post('/projects/{project}/issues/{issue}/tags', [TagController::class, 'assign'])->name('api.tags.assign');