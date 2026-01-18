<?php

namespace App\Filament\Widgets;

use App\Models\Schedule;
use Filament\Actions\Action;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use App\Services\ScheduleAssignmentService;
use App\Filament\Resources\Schedules\ScheduleResource;

class MyUpcomingSchedules extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Jadwal Saya';

    protected function getTableQuery(): Builder
    {
        $user = auth()->user();

        return Schedule::query()
            ->with(['location', 'users'])
            ->upcomingVisible()
            ->when($user, fn (Builder $query) => $query->whereHas('users', fn ($q) => $q->where('users.id', $user->id)));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordClasses(fn (Schedule $record) => $this->liturgicalColorClasses($record->liturgical_color))
            ->columns([
                TextColumn::make('title')
                    ->label('Schedule')
                    ->wrap()
                    ->weight(FontWeight::ExtraBold)
                    ->size(TextSize::Large)
                    ->formatStateUsing(fn ($state) => "🗓️ {$state}"),
                TextColumn::make('location.name')
                    ->label('Location')
                    ->icon('heroicon-o-map-pin')
                    ->weight('medium'),
                TextColumn::make('liturgical_color')
                    ->size(TextSize::ExtraSmall)
                    ->formatStateUsing(fn ($state) => $state ? 'Warna Liturgi: '.ucfirst($state) : '-'),
                Split::make([
                    TextColumn::make('scheduled_date')
                        ->date('l, j M Y')
                        ->label('Date')
                        ->icon('heroicon-o-calendar-days'),
                    TextColumn::make('start_time')
                        ->time('H:i')
                        ->grow(false)
                        ->label('Start')
                        ->icon('heroicon-o-clock'),
                ]),
                Split::make([
                    TextColumn::make('lifecycle_status')
                        ->label('Status')
                        ->badge()
                        ->colors([
                            'gray' => Schedule::STATUS_DRAFT,
                            'warning' => Schedule::STATUS_FULL,
                            'success' => Schedule::STATUS_OPEN,
                            'danger' => [Schedule::STATUS_CANCELLED, Schedule::STATUS_LOCKED],
                        ]),
                    TextColumn::make('capacity')
                        ->label('Capacity')
                        ->getStateUsing(fn (Schedule $record) => "{$record->assigned_count} / {$record->required_personnel}")
                        ->badge()
                        ->color(fn (Schedule $record) => $record->isFull ? 'warning' : 'success'),
                ]),
                Panel::make([
                    TextColumn::make('personnel')
                        ->label('Personnel')
                        ->getStateUsing(fn (Schedule $record) => $record->users->pluck('name')->filter()->values()->all())
                        ->listWithLineBreaks()
                        ->bulleted()
                        ->placeholder('None'),
                ])->collapsible()
                    ->collapsed(),
            ])
            ->recordActions([
                Action::make('google_calendar')
                    ->label('Google Calendar')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Schedule $record) => ScheduleResource::googleCalendarUrl($record))
                    ->openUrlInNewTab(),
                Action::make('download_ics')
                    ->label('Download .ics')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Schedule $record) => ScheduleResource::scheduleIcsUrl($record)),
                Action::make('release')
                    ->label('Lepas')
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Schedule $record) {
                        $user = auth()->user();

                        if (! $user) {
                            throw ValidationException::withMessages(['user' => 'You must be logged in.']);
                        }

                        app(ScheduleAssignmentService::class)->release($record, $user, $user, false);
                    }),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ]);
    }

    private function liturgicalColorClasses(?string $liturgicalColor): string
    {
        return match ($liturgicalColor) {
            'hijau' => 'bg-green-50',
            'merah' => 'bg-red-200',
            'putih' => 'bg-white',
            'merah muda' => 'bg-pink-200',
            'ungu' => 'bg-purple-200',
            default => '', };
    }
}
