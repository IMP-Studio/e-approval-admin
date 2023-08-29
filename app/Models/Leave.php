<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leave extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];

    public function leavestatus()
    {
        return $this->hasOne(LeaveStatus::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function presence()
    {
        return $this->belongsTo(Presence::class);
    }
}
