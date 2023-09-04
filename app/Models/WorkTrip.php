<?php

namespace App\Models;

use App\Models\StatusCommit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkTrip extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'work_trips';
    protected $guarded = ['id'];

    public function presence()
    {
        return $this->belongsTo(Presence::class);
    }
    public function user()
    {
        return $this->belongsTo(user::class);
    }
    public function statusCommit()
    {
        return $this->morphMany(StatusCommit::class, 'statusable');
    }
}
