<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class employee extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];

    public function division()
    {
        return $this->belongsTo(division::class);
    }
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
