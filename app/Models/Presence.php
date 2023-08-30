<?php

namespace App\Models;

use App\Models\Leave;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Presence extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function standup()
    {
        return $this->belongsTo(Standup::class);
    }
    public function telework()
{
    return $this->hasOne(Telework::class);
}
    public function worktrip()
    {
        return $this->hasOne(WorkTrip::class);
    }
    public function leave()
    {
        return $this->hasOne(Leave::class);
    }
}
