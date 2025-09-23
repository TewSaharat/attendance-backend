<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

abstract class Controller
{
    // ฟังก์ชันสมัครสมาชิก (signup)
    public function signup(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'nullable|string',
            'department' => 'nullable|string',
        ]);
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        return response()->json(['user' => $user], 201);
    }

    // ฟังก์ชันเข้าสู่ระบบ (login)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // ตัวอย่าง: สร้าง token (ถ้าใช้ sanctum หรือ passport)
            // $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'Login success',
                'user' => $user,
                // 'token' => $token
            ]);
        }
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
}
