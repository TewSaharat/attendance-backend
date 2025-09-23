<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name','email','password','role'];
    protected $hidden = ['password'];

    public function attendance(){
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests(){
        return $this->hasMany(LeaveRequest::class);
    }
}

