<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'Category_code'
    ];

    protected $hidden = ['password'];

   // app/Models/User.php
    public function profile() {
    return $this->hasOne(Profile::class);
    }

    // สิทธิการลา
    public function leaveRights() {
        return $this->hasOne(LeaveRight::class, 'user_id');
    }

    public function leaves() {
    return $this->hasMany(LeaveRequest::class);
}

    // ขอการลา
    public function leaveRequests(){
        return $this->hasMany(LeaveRequest::class);
    }

    // -----------------------------
    // JWT methods
    // -----------------------------
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

  


    // Hash password อัตโนมัติ
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }
}
