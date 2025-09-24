<?php

// app/Models/Profile.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
  protected $fillable = ['phone', 'address', 'profile_image', 'profile_image_mime'];
    public function user() {
        return $this->belongsTo(User::class);
    }
}

