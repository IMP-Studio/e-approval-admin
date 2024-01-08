<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StandUp extends Model
{

    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $table = 'standups';

    /**
     * Relasi ke tabel Presence
     *
     * @return BelongsTo<Presence,StandUp>
     */
    public function presence()
    {
        return $this->belongsTo(Presence::class);
    }

    /**
     * Relasi ke tabel Project
     *
     * @return BelongsTo<Project,StandUp>
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relasi ke tabel User
     *
     * @return BelongsTo<User,StandUp>
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
