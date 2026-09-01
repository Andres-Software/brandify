<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'directory_id',
        'slug',
        'description',
        'embed_src',
        'embed_raw',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function directory(): BelongsTo
    {
        return $this->belongsTo(Directory::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function getPublicUrlAttribute(): string
    {
        return url("/{$this->directory->slug}/{$this->slug}");
    }
}
