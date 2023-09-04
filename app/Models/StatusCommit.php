<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusCommit extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'status_commits';
    protected $guarded = ['id'];

    public function statusable()
    {
        return $this->morphTo();
    }

    public function approver()
{
    return $this->belongsTo(User::class, 'approver_id');
}
}
