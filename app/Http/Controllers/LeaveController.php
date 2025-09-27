<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LeaveController extends Controller{


    public function leaveTypes()
        {
            $types = LeaveType::all(); // ดึงข้อมูลทั้งหมดจากตาราง leave_types
            return response()->json($types);
        }


    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'supervisor') {
            // supervisor เห็นทุกใบลา
            $leaves = Leave::with('user', 'leaveType')->get();
        } elseif ($user->role === 'manager') {
            // manager เห็นเฉพาะใบลาของ user ปกติ
            $leaves = Leave::with('user', 'leaveType')
                ->whereHas('user', function($q) {
                    $q->where('role', 'user');
                })->get();
        } else {
            // user ไม่สามารถเห็นใบลาคนอื่น
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($leaves);
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
            'status' => 'nullable|string|in:pending,approved,rejected',
        ]);

        $leave = LeaveRequest::create($data);
        return response()->json($leave, 201);
    }

    // ดูรายละเอียดใบลา
    public function show($id)
    {
        $leave = LeaveRequest::with('user', 'leaveType')->findOrFail($id);
        return response()->json($leave);
    }


public function submitLeave(Request $request)
{
    $user = $request->user(); // ดึง user จาก token

    // Validation
    $validated = $request->validate([
        'pronoun' => 'nullable|string',
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'position' => 'nullable|string',
        'department' => 'nullable|string',
        'division' => 'nullable|string',
        'leave_type_id' => 'required|exists:leave_types,id',
        'leave_reason' => 'required|string',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'start_full_day' => 'required|integer|in:1,2',
        'end_full_day' => 'required|integer|in:1,2',
        'contact' => 'nullable|string',
        'files.*' => 'nullable|file|max:2048|mimes:jpeg,png,pdf',
    ]);

    // map field ให้ตรง DB
    $leave = LeaveRequest::create([
        'user_id' => $user->id, // ใช้จาก token
        'pronoun' => $validated['pronoun'] ?? null,
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'],
        'position' => $validated['position'] ?? null,
        'department' => $validated['department'] ?? null,
        'division' => $validated['division'] ?? null,
        'leave_type_id' => $validated['leave_type_id'],
        'reason' => $validated['leave_reason'], // 👈 map ไป column reason
        'start_date' => $validated['start_date'],
        'end_date' => $validated['end_date'],
        'start_full_day' => $validated['start_full_day'],
        'end_full_day' => $validated['end_full_day'],
        'contact' => $validated['contact'] ?? null,
        'status' => 'pending',
    ]);

    // จัดการไฟล์
    $filePaths = [];
    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('leave_files', $filename, 'public');
            $filePaths[] = asset('storage/' . $path);
            $filePaths[] = $path;
        }
    }

    $leave->files = json_encode($filePaths);
    $leave->save();

    return response()->json([
        'message' => 'ส่งใบลาสำเร็จ',
        'leave' => $leave
    ]);
}



    // ✏️ แก้ไขใบลา
public function updateLeave(Request $request, $id)
{
    $leave = LeaveRequest::find($id);
    if (!$leave) {
        return response()->json(['message' => 'ไม่พบใบลา'], 404);
    }

    $leave->pronoun      = $request->pronoun;
    $leave->first_name   = $request->first_name;
    $leave->last_name    = $request->last_name;
    $leave->position     = $request->position;
    $leave->department   = $request->department;
    $leave->division     = $request->division;
    $leave->leave_type_id= $request->leave_type_id;
    $leave->leave_reason = $request->leave_reason;
    $leave->start_date   = Carbon::parse($request->start_date)->format('Y-m-d');
    $leave->start_full_day = $request->start_full_day;
    $leave->end_date     = Carbon::parse($request->end_date)->format('Y-m-d');
    $leave->end_full_day = $request->end_full_day;
    $leave->contact      = $request->contact;

    $leave->save();

    return response()->json($leave);
}


    public function myLeaves(Request $request)
{
    $user = $request->user();
    return response()->json($user->leaveRequests()->with('leaveType')->get());
}


    // 🗑️ ลบใบลา
    public function destroy($id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $leave->delete();
        return response()->json(['message' => 'Leave deleted']);
    }

    //ดึงใบราที่รออนุมัติ
public function allLeaves(Request $request)
{
    $user = $request->user();

    if ($user->role === 'supervisor') {
        return LeaveRequest::with('user', 'leaveType')->get(); // ทุก status
    }

    if ($user->role === 'manager') {
        return LeaveRequest::with('user', 'leaveType')
            ->whereHas('user', fn($q) => $q->where('role', 'user'))
            ->get(); // ทุก status
    }

    return response()->json([], 403);
}

 public function updateApproval(Request $request, $id)
    {
        // ดึงใบลาตาม id
        $leave = LeaveRequest::findOrFail($id);

        // กำหนดสถานะ และค่าต่าง ๆ
        $leave->status = $request->option === 'rejected' ? 'rejected' : 'approved';
        $leave->approval_type = $request->option === 'rejected' ? null : $request->option;
        $leave->reject_reason = $request->rejectReason ?? null;
        $leave->approver_name = $request->approverName ?? null;
        $leave->approver_position = $request->approverPosition ?? null;
        $leave->approval_date = $request->approvalDate ? Carbon::parse($request->approvalDate) : now();

        // บันทึกลงฐานข้อมูล
        $leave->save();


    }


}
