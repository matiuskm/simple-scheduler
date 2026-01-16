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
            Cache::forget(self::CACHE_KEY_ACTIVE . '.list.1');
            Cache::forget(self::CACHE_KEY_ACTIVE . '.list.2');
            Cache::forget(self::CACHE_KEY_ACTIVE . '.list.3');
            Cache::forget(self::CACHE_KEY_ACTIVE . '.list.4');
            Cache::forget(self::CACHE_KEY_ACTIVE . '.list.5');
        });

        static::deleted(function (): void {
            Cache::forget(self::CACHE_KEY_ACTIVE);
            Cache::forget(self::CACHE_KEY_ACTIVE . '.list.1');
            Cache::forget(self::CACHE_KEY_ACTIVE . '.list.2');
            Cache::forget(self::CACHE_KEY_ACTIVE . '.list.3');
            Cache::forget(self::CACHE_KEY_ACTIVE . '.list.4');
            Cache::forget(self::CACHE_KEY_ACTIVE . '.list.5');
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

    public static function activeListCached(int $limit = 3)
    {
        $ttlSeconds = (int) config('scheduler.announcement_cache_seconds', 60);
        $limit = max(1, min(5, $limit));

        return Cache::remember(self::CACHE_KEY_ACTIVE . ".list.{$limit}", $ttlSeconds, function () use ($limit) {
            return self::query()
                ->activeNow()
                ->orderByDesc('start_at')
                ->orderByDesc('id')
                ->limit($limit)
                ->get();
        });
    }
}
