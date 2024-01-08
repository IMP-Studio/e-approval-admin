<?php

namespace App\Models;

use App\Models\Leave;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Presence extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];

    /**
     * Relasi ke tabel User
     *
     * @return BelongsTo<User,Presence>
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Relasi ke tabel StandUp
     *
     * @return HasMany<StandUp>
     */
    public function standup()
    {
        return $this->hasMany(StandUp::class);
    }

    /**
     * Relasi ke table Telework
     *
     * @return HasOne<Telework>
     */
    public function telework()
    {
        return $this->hasOne(Telework::class);
    }

    /**
     * Relasi ke table WorkTrip
     *
     * @return HasOne<WorkTrip>
     */
    public function worktrip()
    {
        return $this->hasOne(WorkTrip::class);
    }

    /**
     * Relasi ke table Leave
     *
     * @return HasOne<Leave>
     */
    public function leave()
    {
        return $this->hasOne(Leave::class);
    }

    /**
     * Relasi ke table StatusCommit
     *
     * @return MorphMany<StatusCommit>
     */
    public function statusCommit()
    {
        return $this->morphMany(StatusCommit::class, 'statusable');
    }
}
