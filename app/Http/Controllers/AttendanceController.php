<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $user = Auth::user();
        $attendance = Attendance::firstOrCreate(
            [
                'user_id' => $user->id,
                'date' => now()->toDateString()
            ],
            [
                'check_in' => now()
            ]
        );

        return response()->json($attendance);
    }

    public function checkOut(Request $request)
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', now()->toDateString())
            ->whereNull('check_out')
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'No check-in found'], 404);
        }

        $attendance->check_out = now();
        $attendance->save();

        return response()->json($attendance);
    }
}
