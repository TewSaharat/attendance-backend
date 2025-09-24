<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;

// Auth
Route::post('signup', [AuthController::class, 'signup']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

// routes ที่ต้อง login (JWT) ถึงเข้าถึงได้
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/me', function() {
        return auth()->user();
    });
    Route::put('/me', [AuthController::class, 'updateProfile']);
});
// Attendance
Route::post('attendance/check-in', [AttendanceController::class, 'checkIn'])->middleware('auth:sanctum');
Route::post('attendance/check-out', [AttendanceController::class, 'checkOut'])->middleware('auth:sanctum');

// authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('attendance/check-out', [AttendanceController::class, 'checkOut']);
});


// Leave
Route::get('leaves', [LeaveController::class, 'index'])->middleware('auth:sanctum');
Route::post('leaves', [LeaveController::class, 'store'])->middleware('auth:sanctum');
Route::get('leaves/{id}', [LeaveController::class, 'show'])->middleware('auth:sanctum');
Route::put('leaves/{id}', [LeaveController::class, 'update'])->middleware('auth:sanctum');
Route::delete('leaves/{id}', [LeaveController::class, 'destroy'])->middleware('auth:sanctum');
Route::post('leaves/{id}/approve', [LeaveController::class, 'approve'])->middleware('auth:sanctum');
Route::post('leaves/{id}/reject', [LeaveController::class, 'reject'])->middleware('auth:sanctum');


