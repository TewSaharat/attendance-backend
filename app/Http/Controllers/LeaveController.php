<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    // แสดงรายการใบลา
    public function index()
    {
        return response()->json(\App\Models\LeaveRequest::with('user', 'leaveType')->get());
    }

    // สร้างใบลาใหม่
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'status' => 'nullable|string',
        ]);
        $leave = \App\Models\LeaveRequest::create($data);
        return response()->json($leave, 201);
    }

    // ดูรายละเอียดใบลา
    public function show($id)
    {
        $leave = \App\Models\LeaveRequest::with('user', 'leaveType')->findOrFail($id);
        return response()->json($leave);
    }

    // อัปเดตใบลา
    public function update(Request $request, $id)
    {
        $leave = \App\Models\LeaveRequest::findOrFail($id);
        $data = $request->validate([
            'leave_type_id' => 'sometimes|exists:leave_types,id',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'status' => 'nullable|string',
        ]);
        $leave->update($data);
        return response()->json($leave);
    }

    // ลบใบลา
    public function destroy($id)
    {
        $leave = \App\Models\LeaveRequest::findOrFail($id);
        $leave->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // อนุมัติใบลา
    public function approve($id)
    {
        $leave = \App\Models\LeaveRequest::findOrFail($id);
        $leave->status = 'อนุมัติแล้ว';
        $leave->save();
        return response()->json($leave);
    }

    // ปฏิเสธใบลา
    public function reject($id)
    {
        $leave = \App\Models\LeaveRequest::findOrFail($id);
        $leave->status = 'ถูกปฏิเสธ';
        $leave->save();
        return response()->json($leave);
    }
}
