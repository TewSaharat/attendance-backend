<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveType; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;  


class LeaveTypeController extends Controller
{
    //// ดึงข้อมูล leave types ทั้งหมด
    public function index()
    {
        $leaveTypes = LeaveType::all(); // ดึงข้อมูลทั้งหมดจาก DB
        return response()->json($leaveTypes);
    }
}
