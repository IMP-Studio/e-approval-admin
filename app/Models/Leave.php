<?php

namespace App\Models;

use App\Models\StatusCommit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Leave extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function presence()
    {
        return $this->belongsTo(Presence::class);
    }
    public function leavedetail()
{
    return $this->belongsTo(LeaveDetail::class, 'leave_detail_id', 'id');
}
    public function statusCommit()
    {
        return $this->morphMany(StatusCommit::class, 'statusable');
    }

    public function substitute()
    {
        return $this->belongsTo(User::class, 'substitute_id');
    }
}
