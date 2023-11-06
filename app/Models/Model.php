<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Model extends Eloquent
{
    use HasFactory;

    /**
     * The attributes that are guarded
     *
     * @var array<string>
     */
    protected $guarded = [];

    public function getCreatedAtAttribute(?string $value): string|false
    {
        // @phpstan-ignore-next-line
        return date('Y-m-d H:i:s', strtotime($value));
    }

    public function getUpdatedAtAttribute(?string $value): string|false
    {
        // @phpstan-ignore-next-line
        return date('Y-m-d H:i:s', strtotime($value));
    }
}
