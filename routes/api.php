<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GardenController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\PlotController;
use App\Http\Controllers\Api\JournalController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\JoinRequestController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| Rute API
|--------------------------------------------------------------------------
*/

// --- Rute Publik ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/gardens/public', [GardenController::class, 'publicIndex']);


// --- Rute yang Membutuhkan Autentikasi (Login) ---
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/users/search', [UserController::class, 'search']);

    // CRUD Utama
    Route::apiResource('/gardens', GardenController::class);
    Route::apiResource('/plots', PlotController::class);
    Route::apiResource('/memberships', MembershipController::class)->only(['store', 'destroy']);
    
    // Rute Jurnal yang Sudah Diperbaiki
    Route::get('/gardens/{gardenId}/journals', [JournalController::class, 'index']);
    Route::apiResource('/journals', JournalController::class)->only(['store', 'show', 'update', 'destroy']);
    
    // Pengumuman & Jadwal
    Route::apiResource('/announcements', AnnouncementController::class);
    Route::patch('/schedules/{schedule}/status', [ScheduleController::class, 'updateStatus']);

    Route::get('/gardens/{garden}/schedules', [ScheduleController::class, 'index']);
    Route::apiResource('/schedules', ScheduleController::class)->only(['store', 'show', 'destroy']);
    // Rute untuk update status tetap sama
    Route::patch('/schedules/{schedule}/status', [ScheduleController::class, 'updateStatus']);

    Route::get('/gardens/{garden}/memberships', [MembershipController::class, 'index']);
    Route::apiResource('/memberships', MembershipController::class)->only(['store', 'destroy']);
    // Permintaan Bergabung
    Route::post('/join-requests', [JoinRequestController::class, 'store']);
    Route::get('/join-requests', [JoinRequestController::class, 'index']);
    Route::post('/join-requests/{joinRequest}/approve', [JoinRequestController::class, 'approve']);
    Route::post('/join-requests/{joinRequest}/reject', [JoinRequestController::class, 'reject']);
});