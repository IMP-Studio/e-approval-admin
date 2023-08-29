<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkTrip extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'work_trips';
    protected $guarded = ['id'];

    public function presence()
    {
        return $this->belongsTo(Presence::class);
    }
}
