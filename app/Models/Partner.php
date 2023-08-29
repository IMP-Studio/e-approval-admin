<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
