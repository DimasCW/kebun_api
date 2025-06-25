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

    // CRUD Utama
    Route::apiResource('/gardens', GardenController::class);
    Route::apiResource('/plots', PlotController::class);
    Route::apiResource('/journals', JournalController::class);
    Route::apiResource('/memberships', MembershipController::class)->only(['store', 'destroy']);
    
    // Pengumuman & Jadwal
    Route::apiResource('/announcements', AnnouncementController::class);
    Route::apiResource('/schedules', ScheduleController::class)->except(['update']);
    Route::patch('/schedules/{schedule}/status', [ScheduleController::class, 'updateStatus']); // Rute khusus untuk update status

    // Permintaan Bergabung
    Route::post('/join-requests', [JoinRequestController::class, 'store']);
    Route::get('/join-requests', [JoinRequestController::class, 'index']);
    Route::post('/join-requests/{joinRequest}/approve', [JoinRequestController::class, 'approve']);
    Route::post('/join-requests/{joinRequest}/reject', [JoinRequestController::class, 'reject']);
});