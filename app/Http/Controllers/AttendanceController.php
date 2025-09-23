<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    // เช็คอิน (บันทึกเวลาเข้า)
    public function checkIn(Request $request)
    {
        $user = Auth::user();
        $attendance = Attendance::firstOrCreate([
            'user_id' => $user->id,
            'date' => now()->toDateString()
        ]);
        $attendance->check_in = now();
        $attendance->save();
        return response()->json($attendance);
    }

    // เช็คเอาท์ (บันทึกเวลาออก)
    public function checkOut(Request $request)
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', now()->toDateString())
            ->first();
        if(!$attendance){
            return response()->json(['message'=>'No check-in found'], 404);
        }
        $attendance->check_out = now();
        $attendance->save();
        return response()->json($attendance);
    }
}
