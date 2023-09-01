<?php

namespace App\Models;

use App\Models\Presence;
use App\Models\StatusCommit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Telework extends Model
{
    use HasFactory, SoftDeletes;
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
