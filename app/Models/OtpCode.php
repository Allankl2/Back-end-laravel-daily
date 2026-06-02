<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    protected $fillable = ['email', 'code', 'attempts', 'expires_at', 'blocked_at'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'blocked_at' => 'datetime',
        ];
    }
}
