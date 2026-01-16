<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Announcement extends Model
{
    use HasFactory;

    public const CACHE_KEY_ACTIVE = 'announcement.active';

    protected $fillable = [
        'message',
        'start_at',
        'end_at',
        'background_color',
        'is_enabled',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_enabled' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function (): void {
            Cache::forget(self::CACHE_KEY_ACTIVE);
        });

        static::deleted(function (): void {
            Cache::forget(self::CACHE_KEY_ACTIVE);
        });
    }

    public function scopeActiveNow($query)
    {
        return $query
            ->where('is_enabled', true)
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now());
    }

    public static function activeCached(): ?self
    {
        $ttlSeconds = (int) config('scheduler.announcement_cache_seconds', 60);

        return Cache::remember(self::CACHE_KEY_ACTIVE, $ttlSeconds, function (): ?self {
            return self::query()
                ->activeNow()
                ->orderByDesc('start_at')
                ->orderByDesc('id')
                ->first();
        });
    }
}
