<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use Illuminate\Http\Request;
use App\Http\Controllers\LeaveTypeController;

// Auth
Route::post('signup', [AuthController::class, 'signup']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

// routes ที่ต้อง login (JWT) ถึงเข้าถึงได้
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
   Route::middleware('auth:api')->get('/me', function(Request $request) {
    $user = $request->user()->load('profile'); // ใช้ $request->user()
    return response()->json($user);
});
    Route::post('/updateprofile', [AuthController::class, 'updateProfile']);
    
});
// Attendance
Route::post('attendance/check-in', [AttendanceController::class, 'checkIn'])->middleware('auth:sanctum');
Route::post('attendance/check-out', [AttendanceController::class, 'checkOut'])->middleware('auth:sanctum');

// authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('attendance/check-out', [AttendanceController::class, 'checkOut']);
});


// ================= LEAVE REQUESTS ================= //
Route::middleware('auth:api')->prefix('leaves')->group(function () {
    Route::post('/', [LeaveController::class, 'submitLeave']); // ส่งใบลา
    Route::get('{id}', [LeaveController::class, 'show']);      
    Route::delete('{id}', [LeaveController::class, 'destroy']);
    Route::put('{id}/updateLeave', [LeaveController::class, 'updateLeave']);
   

    // อนุมัติ / ปฏิเสธ
Route::post('{id}/approve', [LeaveController::class, 'updateApproval'])
    ->middleware(\App\Http\Middleware\LeaveApprovalRole::class);

});

Route::get('/leave-types', [LeaveTypeController::class, 'index']);

Route::middleware('auth:api')->get('/my-leaves', [LeaveController::class, 'myLeaves']);

Route::middleware('auth:api')->prefix('api')->group(function () {
    Route::get('leaves/allLeaves', [LeaveController::class, 'allLeaves']);
});


Route::get('/api/files/{id}', function ($id) {
    $filePath = storage_path("app/files/{$id}.pdf");
    
    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath); 
});
