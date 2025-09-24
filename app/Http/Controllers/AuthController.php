<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // Signup
    public function signup(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'username' => 'nullable|string|unique:users,username',
                'password' => 'required|string|min:6',
                'Category_code' => 'nullable|string'
            ]);

            // สร้าง user (model จะ hash password ให้เอง)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => $request->password,
                'Category_code' => $request->Category_code ?? 'all',
                'role' => 'user',
            ]);
            // สร้าง profile ว่างสำหรับ user ใหม่
            $user->profile()->create([]); 

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $token
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Login
    public function login(Request $request){
        $data = $request->validate([
            'email' => 'nullable|email',
            'username' => 'nullable|string',
            'password' => 'required|string',
        ]);

        if (empty($data['email']) && empty($data['username'])) {
            return response()->json(['message' => 'Email or username is required'], 422);
        }

        $field = !empty($data['email']) ? 'email' : 'username';
        $credentials = [$field => $data[$field], 'password' => $data['password']];

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'message' => 'Login success',
            'user' => auth('api')->user(),
            'token' => $token
        ]);
    }

    // Logout
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Logged out successfully']);
    }

    // Current user
    public function me()
    {
        $user = auth()->user()->load('profile');
        return response()->json($user);
    }

    // Update profile
public function updateProfile(Request $request)
{
    $user = $request->user();
    $profile = $user->profile ?? $user->profile()->create([]);

    $data = $request->validate([
        'name' => 'sometimes|string|max:255',
        'email' => 'sometimes|email|unique:users,email,' . $user->id,
        'department' => 'nullable|string|max:255',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
        'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // อัพเดตข้อมูล user
    $user->update([
        'name' => $data['name'] ?? $user->name,
        'email' => $data['email'] ?? $user->email,
        'department' => $data['department'] ?? $user->department,
    ]);

    // อัพเดตข้อมูล profile
    $profile->phone = $data['phone'] ?? $profile->phone;
    $profile->address = $data['address'] ?? $profile->address;

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            if ($file->isValid()) {
                $profile->profile_image = base64_encode(file_get_contents($file));
                $profile->profile_image_mime = $file->getClientMimeType();
            } else {
                \Log::error('Invalid file uploaded');
            }
        } else {
            \Log::error('No file uploaded');
        }


    $profile->save();


    \Log::info('Saved profile image:', [
    'mime' => $profile->profile_image_mime,
    'base64' => substr($profile->profile_image, 0, 100) // แสดงแค่บางส่วน
    ]);


    
    // สร้าง URL Base64 สำหรับ Angular
    $profile_image_url = $profile->profile_image
    ? 'data:' . $profile->profile_image_mime . ';base64,' . $profile->profile_image
    : null;
    
    return response()->json([
        'message' => 'Profile updated successfully',
        'user' => $user->load('profile'),
        'profile_image_url' => $profile_image_url,
    ]);
}

// AuthController.php
public function getProfile(Request $request) {
    $user = $request->user()->load('profile', 'leaves.leaveType');

    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'department' => $user->department,
        'rights' => $user->profile ?? new \stdClass(), // profile -> สิทธิการลางาน
        'leaves' => $user->leaves->map(function($l) {
            return [
                'id' => $l->id,
                'type' => $l->leaveType->name,
                'start' => $l->start_date,
                'end' => $l->end_date,
                'status' => $l->status,
                'requestDate' => $l->created_at->format('Y-m-d')
            ];
        })
    ]);
}



}
