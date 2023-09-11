<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'otp_verification';
    protected $fillable = [
        'id',
        'user_id',
        'otp_code',
        'expiry_time',
        'is_verified'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
