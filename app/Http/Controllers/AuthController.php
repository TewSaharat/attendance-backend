<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // สมัครสมาชิก (Signup)
    public function signup(Request $request)
    {
        // เรียกใช้ signup จาก base Controller
        return parent::signup($request);
    }

    // เข้าสู่ระบบ (Login)
    public function login(Request $request)
    {
        // เรียกใช้ login จาก base Controller
        return parent::login($request);
    }

    // ออกจากระบบ (Logout)
    public function logout(Request $request)
    {
        \Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'Logged out']);
    }
}
