<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Schedule extends Model {
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
        return $this->assigned_count >= $this->required_personnel;
    }
}
