<?php

namespace App\Models;

use Carbon\Carbon;
use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentRequest extends Model
{
    use HasFactory;

    public const STATUS_REQUESTED = 'requested';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'schedule_id',
        'user_id',
        'status',
        'reason',
        'decided_by',
        'decided_at',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function approve(User $decider): void
    {
        if ($this->status !== self::STATUS_REQUESTED) {
            throw new DomainException('Only requested assignments can be approved.');
        }

        $schedule = $this->schedule;
        $schedule->assertCanAssign(true);

        $schedule->users()->syncWithoutDetaching([
            $this->user_id => [
                'assigned_by' => $decider->id,
            ],
        ]);

        $this->status = self::STATUS_APPROVED;
        $this->decided_by = $decider->id;
        $this->decided_at = Carbon::now();
        $this->save();

        $schedule->logAudit('assignment_request_approved', [
            'request_id' => $this->id,
            'user_id' => $this->user_id,
        ]);
    }

    public function reject(User $decider, ?string $reason = null): void
    {
        if ($this->status !== self::STATUS_REQUESTED) {
            throw new DomainException('Only requested assignments can be rejected.');
        }

        $this->status = self::STATUS_REJECTED;
        $this->decided_by = $decider->id;
        $this->decided_at = Carbon::now();
        $this->reason = $reason ?? $this->reason;
        $this->save();

        $this->schedule->logAudit('assignment_request_rejected', [
            'request_id' => $this->id,
            'user_id' => $this->user_id,
        ]);
    }
}
