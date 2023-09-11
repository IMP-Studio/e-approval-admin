<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StandUp extends Model
{

    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $table = 'standups';

    public function presence()
    {
        return $this->belongsTo(Presence::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
