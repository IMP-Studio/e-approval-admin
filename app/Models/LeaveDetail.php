<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $table = 'leave_detail';

    public function typeofleave()
    {
        return $this->belongsTo(TypeOfLeave::class,  'type_of_leave_id', 'id');
    }

    public function leave()
    {
        return $this->hasMany(Leave::class);
    }
}
