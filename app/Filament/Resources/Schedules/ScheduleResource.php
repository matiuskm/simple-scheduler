<?php

namespace App\Filament\Resources\Schedules;

use App\Filament\Resources\Schedules\Pages\CreateSchedule;
use App\Filament\Resources\Schedules\Pages\EditSchedule;
use App\Filament\Resources\Schedules\Pages\ListSchedules;
use App\Filament\Resources\Schedules\RelationManagers\UsersRelationManager;
use App\Filament\Resources\Schedules\RelationManagers\AuditLogsRelationManager;
use App\Filament\Resources\Schedules\Schemas\ScheduleForm;
use App\Filament\Resources\Schedules\Tables\SchedulesTable;
use Carbon\CarbonImmutable;
use App\Models\Schedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static string|UnitEnum|null $navigationGroup = 'Management';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return ScheduleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchedulesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            // UsersRelationManager::class,
            AuditLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSchedules::route('/'),
            'create' => CreateSchedule::route('/create'),
            'edit' => EditSchedule::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool {
        return auth()->user()?->isAdmin();
    }

    public static function googleCalendarUrl(Schedule $schedule): string
    {
        $timezone = 'Asia/Jakarta';

        $start = $schedule->starts_at->setTimezone($timezone);
        $end = ($schedule->ends_at ?? $start->addMinutes(90))->setTimezone($timezone);

        $location = $schedule->location?->name ?? '-';
        $details = "Jadwal tugas: {$schedule->title} | Lokasi: {$location}";

        $params = [
            'action' => 'TEMPLATE',
            'text' => $schedule->title,
            'dates' => $start->format('Ymd\THis') . '/' . $end->format('Ymd\THis'),
            'details' => $details,
            'location' => $location,
        ];

        return 'https://calendar.google.com/calendar/render?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    public static function scheduleIcsUrl(Schedule $schedule): string
    {
        return route('schedules.calendar.ics', $schedule);
    }
}
