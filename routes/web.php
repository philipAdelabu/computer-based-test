<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\Admin\LocalGovernmentController;
use App\Http\Controllers\Admin\WardController;
use App\Http\Controllers\Admin\VotingUnitController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes - Using middleware with full class name
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Vote routes - using full class name
    Route::get('/vote/create', [VoteController::class, 'create'])
        ->name('vote.create')
        ->middleware(CheckRole::class . ':admin,officer');
    
    Route::post('/vote', [VoteController::class, 'store'])
        ->name('vote.store')
        ->middleware(CheckRole::class . ':admin,officer');
    
    // AJAX routes for dynamic dropdowns
    Route::get('/wards/{lgId}', [VoteController::class, 'getWards'])->name('ajax.wards');
    Route::get('/voting-units/{wardId}', [VoteController::class, 'getVotingUnits'])->name('ajax.units');
});

// Admin only routes - using full class name
Route::middleware(['auth', CheckRole::class . ':admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    Route::resource('local-governments', LocalGovernmentController::class);
    Route::resource('wards', WardController::class);
    Route::resource('voting-units', VotingUnitController::class);
    
    Route::get('/api/wards/{lgId}', [WardController::class, 'getByLocalGovernment']);
    Route::get('/api/voting-units/{wardId}', [VotingUnitController::class, 'getByWard']);
});