<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;

class LeaveApprovalRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $leaveId = $request->route('id');
        $leave = LeaveRequest::findOrFail($leaveId);

        // Supervisor อนุมัติได้ทุกใบ
        if ($user->role === 'supervisor') {
            return $next($request);
        }

        // Manager อนุมัติได้เฉพาะใบลา user
        if ($user->role === 'manager') {
            if ($leave->user->role === 'user') {
                return $next($request);
            }
            return response()->json(['message' => 'ไม่อนุญาตสำหรับใบลานี้'], 403);
        }

        // User ไม่สามารถอนุมัติได้
        return response()->json(['message' => 'คุณไม่มีสิทธิ์อนุมัติ'], 403);
    }
}
