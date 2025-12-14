<?php

namespace App\Services;

use App\Models\Schedule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ScheduleConflictDetector
{
    public function locationConflicts(Schedule $schedule): Collection
    {
        $startTime = $this->asTimeString($schedule->start_time);
        $endTime = $this->asTimeString($schedule->end_time ?? $schedule->start_time);

        return Schedule::query()
            ->where('id', '!=', $schedule->id)
            ->where('location_id', $schedule->location_id)
            ->whereDate('scheduled_date', $schedule->scheduled_date)
            ->whereNotIn('status', [Schedule::STATUS_CANCELLED, Schedule::STATUS_COMPLETED])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereRaw(
                    'start_time < ? AND COALESCE(end_time, start_time) > ?',
                    [$endTime, $startTime]
                );
            })
            ->select(['id', 'title', 'start_time', 'end_time', 'status'])
            ->get();
    }

    public function personnelConflicts(Schedule $schedule): Collection
    {
        $startTime = $this->asTimeString($schedule->start_time);
        $endTime = $this->asTimeString($schedule->end_time ?? $schedule->start_time);

        return DB::table('schedule_user as su')
            ->join('schedule_user as other_su', 'su.user_id', '=', 'other_su.user_id')
            ->join('schedules as other', 'other.id', '=', 'other_su.schedule_id')
            ->where('su.schedule_id', $schedule->id)
            ->where('other.id', '!=', $schedule->id)
            ->whereDate('other.scheduled_date', $schedule->scheduled_date)
            ->whereNotIn('other.status', [Schedule::STATUS_CANCELLED, Schedule::STATUS_COMPLETED])
            ->whereRaw('other.start_time < ?', [$endTime])
            ->whereRaw('COALESCE(other.end_time, other.start_time) > ?', [$startTime])
            ->select(['other.id', 'other.title', 'other.start_time', 'other.end_time', 'other.status', 'su.user_id'])
            ->get()
            ->groupBy('user_id');
    }

    public function summary(Schedule $schedule): array
    {
        $location = $this->locationConflicts($schedule);
        $personnel = $this->personnelConflicts($schedule);

        return [
            'location_count' => $location->count(),
            'personnel_count' => $personnel->count(),
            'has_conflicts' => $location->count() > 0 || $personnel->count() > 0,
        ];
    }

    private function asTimeString($value): string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('H:i:s');
        }

        return (string) $value;
    }
}
