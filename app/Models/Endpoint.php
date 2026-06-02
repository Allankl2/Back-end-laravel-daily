<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Endpoint extends Model
{
    protected $fillable = ['name', 'method', 'route', 'description'];

    public function screens(): BelongsToMany
    {
        return $this->belongsToMany(Screen::class);
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class);
    }
}
