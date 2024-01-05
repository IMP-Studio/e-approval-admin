<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relasi ke table employee
     *
     * @return HasOne<Employee>
     */
    public function employee()
    {
        return $this->hasOne(Employee::class)->withTrashed();
    }

    /**
     * Relasi ke tabel Presence
     *
     * @return HasMany<Presence>
     */
    public function presence()
    {
        return $this->hasMany(Presence::class);
    }

    /**
     * Relasi ke tabel Telework
     *
     * @return HasMany<Telework>
     */
    public function telework(): HasMany
    {
        return $this->hasMany(Telework::class);
    }

    /**
     * Relasi ke tabel WorkTrip
     *
     * @return HasMany<WorkTrip>
     */
    public function workTrip()
    {
        return $this->hasMany(WorkTrip::class);
    }

    /**
     * Relasi ke tabel Leave
     *
     * @return HasMany<Leave>
     */
    public function leave()
    {
        return $this->hasMany(Leave::class, 'user_id');
    }

    /**
     * Relasi ke tabel StandUp
     *
     * @return HasMany<StandUp>
     */
    public function standups()
    {
        return $this->hasMany(StandUp::class);
    }

    /**
     * Relasi ke tabel OtpVerification
     *
     * @return HasMany<OtpVerification>
     */
    public function otpVerification()
    {
        return $this->hasMany(OtpVerification::class);
    }

    /**
     * Relasi ke tabel Leave
     *
     * @return HasMany<Leave>
     */
    public function subtituteLeave()
    {
        return $this->hasMany(Leave::class, 'substitute_id');
    }
}
