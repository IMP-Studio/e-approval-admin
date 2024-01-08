<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];

    /**
     * Relasi ke tabel Division
     *
     * @return BelongsTo<Division,Employee>
     */
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Relasi ke tabel Position
     *
     * @return BelongsTo<Position,Employee>
     */
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    /**
     * Relasi ke tabel User
     *
     * @return BelongsTo<User,Employee>
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

}
