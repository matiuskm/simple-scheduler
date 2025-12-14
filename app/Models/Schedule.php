<?php

namespace App\Models;

use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class Schedule extends Model {
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_OPEN = 'open';
    public const STATUS_FULL = 'full';
    public const STATUS_LOCKED = 'locked';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'title',
        'description',
        'scheduled_date',
        'start_time',
        'end_time',
        'location_id',
        'status',
        'required_personnel'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function isPublished(): bool {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function location(): BelongsTo {
        return $this->belongsTo(Location::class);
    }

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class)
            ->withTimestamps()
            ->withPivot('assigned_by');
    }

    public function scopeUpcoming($query) {
        return $query->whereDate('scheduled_date', '>', today()->toDateString())
            ->orderBy('scheduled_date')
            ->orderBy('start_time');
    }

    public function getAssignedCountAttribute(): int {
        return $this->users()->count();
    }

    public function getIsFullAttribute(): bool {
        return $this->lifecycle_status === self::STATUS_FULL;
    }

    public function getIsOpenAttribute(): bool {
        return $this->lifecycle_status === self::STATUS_OPEN;
    }

    public function getStartsAtAttribute(): Carbon
    {
        $date = $this->scheduled_date instanceof Carbon
            ? $this->scheduled_date
            : Carbon::parse($this->scheduled_date);

        $time = $this->start_time instanceof Carbon
            ? $this->start_time->format('H:i:s')
            : (string) $this->start_time;

        return $date->copy()->setTimeFromTimeString($time);
    }

    public function getEndsAtAttribute(): ?Carbon
    {
        if (! $this->end_time) {
            return null;
        }

        $date = $this->scheduled_date instanceof Carbon
            ? $this->scheduled_date
            : Carbon::parse($this->scheduled_date);

        $time = $this->end_time instanceof Carbon
            ? $this->end_time->format('H:i:s')
            : (string) $this->end_time;

        return $date->copy()->setTimeFromTimeString($time);
    }

    public function getIsLockedAttribute(): bool
    {
        if (in_array($this->status, [self::STATUS_LOCKED, self::STATUS_COMPLETED, self::STATUS_CANCELLED], true)) {
            return true;
        }

        $lockWindow = (int) config('scheduler.lock_window_minutes', 0);

        if ($lockWindow <= 0) {
            return false;
        }

        return now()->gte($this->starts_at->copy()->subMinutes($lockWindow));
    }

    public function getLifecycleStatusAttribute(): string
    {
        if ($this->status === self::STATUS_DRAFT) {
            return self::STATUS_DRAFT;
        }

        if (in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED], true)) {
            return $this->status;
        }

        if ($this->is_locked) {
            return self::STATUS_LOCKED;
        }

        if ($this->assigned_count >= $this->required_personnel) {
            return self::STATUS_FULL;
        }

        return self::STATUS_OPEN;
    }

    public function canAssign(bool $asAdmin = false): bool {
        if (in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED], true)) {
            return false;
        }

        if (! $asAdmin && $this->is_locked) {
            return false;
        }

        return $this->assigned_count < $this->required_personnel;
    }

    public function assertCanAssign(bool $asAdmin = false): void {
        if (! $this->canAssign($asAdmin)) {
            $reason = $this->is_locked
                ? 'Schedule is locked before start.'
                : 'Schedule is full or unavailable.';

            throw new DomainException($reason);
        }
    }

    public function scopeNeedingPersonnel($query)
    {
        return $query->whereRaw(
            '(select count(*) from schedule_user where schedule_user.schedule_id = schedules.id) < required_personnel'
        );
    }
}
