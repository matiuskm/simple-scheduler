<?php

namespace App\Services;

use App\Models\Schedule;
use Carbon\CarbonImmutable;

class IcsCalendarService
{
    private const TIMEZONE = 'Asia/Jakarta';

    public function makeScheduleIcs(Schedule $schedule): string
    {
        $start = $this->formatDateTimeJakarta(
            $schedule->scheduled_date,
            $schedule->start_time
        );

        $endTime = $schedule->end_time
            ? $this->formatDateTimeJakarta($schedule->scheduled_date, $schedule->end_time)
            : $start->addMinutes(90);

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Prodiakon//Schedule//ID',
            'BEGIN:VEVENT',
            'UID:' . $this->buildUid($schedule),
            'DTSTAMP:' . CarbonImmutable::now('UTC')->format('Ymd\THis\Z'),
            'DTSTART;TZID=' . self::TIMEZONE . ':' . $start->format('Ymd\THis'),
            'DTEND;TZID=' . self::TIMEZONE . ':' . $endTime->format('Ymd\THis'),
            'SUMMARY:' . $this->escapeIcsText($schedule->title ?? 'Schedule'),
            'LOCATION:' . $this->escapeIcsText($this->resolveLocation($schedule)),
            'DESCRIPTION:' . $this->escapeIcsText($schedule->liturgical_color ?? ''),
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        return implode("\r\n", $lines) . "\r\n";
    }

    public function formatDateTimeJakarta($date, $time): CarbonImmutable
    {
        $dateString = $date instanceof \DateTimeInterface
            ? $date->format('Y-m-d')
            : (string) $date;

        $timeString = $time instanceof \DateTimeInterface
            ? $time->format('H:i:s')
            : (string) $time;

        if (strlen($timeString) === 5) {
            $timeString .= ':00';
        }

        return CarbonImmutable::createFromFormat(
            'Y-m-d H:i:s',
            "{$dateString} {$timeString}",
            self::TIMEZONE
        );
    }

    public function escapeIcsText(string $text): string
    {
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace(';', '\;', $text);
        $text = str_replace(',', '\,', $text);
        $text = str_replace(["\r\n", "\n", "\r"], '\n', $text);

        return $text;
    }

    public function buildUid(Schedule $schedule): string
    {
        $appUrl = config('app.url');
        $host = parse_url($appUrl, PHP_URL_HOST) ?: 'localhost';

        return "schedule-{$schedule->id}@{$host}";
    }

    private function resolveLocation(Schedule $schedule): string
    {
        if ($schedule->relationLoaded('location') || $schedule->location) {
            return $schedule->location?->name ?? '-';
        }

        return '-';
    }
}
