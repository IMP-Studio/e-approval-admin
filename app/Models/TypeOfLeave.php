<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeOfLeave extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'type_of_leave';
    public function leavedetail()
    {
        return $this->hasMany(LeaveDetail::class);
    }
}
