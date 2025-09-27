<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $fillable = [
        'user_id',
        'leave_type_id',
        'pronoun',
        'first_name',
        'last_name',
        'position',
        'department',
        'division',
        'leave_reason',
        'reason',
        'start_date',
        'end_date',
        'start_full_day',
        'end_full_day',
        'contact',
        'files',
        'status',
        'approval_type',
        'reject_reason',
        'approver_name',
        'approver_position',
        'approval_date'

    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function leaveType() {
        return $this->belongsTo(LeaveType::class);
    }

}
