<?php

namespace App\Filament\Widgets;

use App\Models\Schedule;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class AdminUpcomingSchedules extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Jadwal Mendatang (Admin)';

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user?->isAdmin() ?? false;
    }

    protected function getTableQuery(): Builder
    {
        $jakartaNow = now('Asia/Jakarta');
        $jakartaToday = $jakartaNow->toDateString();

        return Schedule::query()
            ->with(['location', 'users'])
            ->upcomingVisible()
            ->where('status', Schedule::STATUS_PUBLISHED)
            ->where(function (Builder $query) use ($jakartaNow, $jakartaToday): void {
                $query
                    ->whereDate('scheduled_date', '>', $jakartaToday)
                    ->orWhere(function (Builder $query) use ($jakartaNow, $jakartaToday): void {
                        $query
                            ->whereDate('scheduled_date', $jakartaToday)
                            ->whereTime('start_time', '>=', $jakartaNow->format('H:i:s'));
                    });
            });
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
                        ->date('D, j M Y')
                        ->label('Date')
                        ->icon('heroicon-o-calendar-days'),
                    TextColumn::make('start_time')
                        ->time()
                        ->grow(false)
                        ->formatStateUsing(fn ($state) => date('H:i', strtotime($state)))
                        ->label('Start')
                        ->icon('heroicon-o-clock'),
                ]),
                Split::make([
                    TextColumn::make('lifecycle_status')
                        ->label('Status')
                        ->badge()
                        ->colors([
                            'success' => Schedule::STATUS_OPEN,
                            'warning' => Schedule::STATUS_FULL,
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
                        ->placeholder('None assigned'),
                ])->collapsible()->collapsed(),
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
            'hijau' => 'bg-hijau',
            'merah' => 'bg-merah',
            'putih' => 'bg-putih',
            'merah muda' => 'bg-pink',
            'ungu' => 'bg-ungu',
            default => '',
        };
    }
}
