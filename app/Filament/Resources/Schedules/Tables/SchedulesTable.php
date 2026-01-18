<?php

namespace App\Filament\Resources\Schedules\Tables;

use App\Models\Schedule;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Services\ScheduleConflictDetector;
use Filament\Support\Colors\Color;
use Illuminate\Support\Str;

class SchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('scheduled_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('start_time')
                    ->time('H:i'),
                TextColumn::make('location.name')
                    ->label('Location'),
                TextColumn::make('status')
                    ->state(fn(Schedule $record) => $record->lifecycle_status)
                    ->badge()
                    ->colors([
                        'gray' => Schedule::STATUS_DRAFT,
                        'success' => [Schedule::STATUS_OPEN, Schedule::STATUS_COMPLETED],
                        'warning' => Schedule::STATUS_FULL,
                        'info' => Schedule::STATUS_PUBLISHED,
                        'danger' => [Schedule::STATUS_LOCKED, Schedule::STATUS_CANCELLED],
                    ]),
                TextColumn::make('capacity')
                    ->getStateUsing(fn(Schedule $record) => "{$record->assigned_count} / {$record->required_personnel}")
                    ->badge()
                    ->color(fn(Schedule $record) => $record->isFull ? 'warning' : 'success'),
                TextColumn::make('liturgical_color')
                    ->label('Liturgical')
                    ->badge()
                    ->formatStateUsing(fn($state) => ucfirst($state ?? '-'))
                    ->extraAttributes(fn(Schedule $record) => [
                        'class' => self::liturgicalBadgeClasses($record->liturgical_color),
                    ]),
            ])
            ->filters([
                Filter::make('upcoming')
                    ->label('Upcoming only')
                    ->toggle()
                    ->default(true)
                    ->query(fn(Builder $query): Builder => $query->upcoming()),
                Filter::make('needs_personnel')
                    ->label('Needs personnel')
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->needingPersonnel()),
                Filter::make('has_conflicts')
                    ->label('Has conflicts')
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->hasConflicts()),
            ])
            ->recordActions([
                EditAction::make()
                    ->disabled(fn(Schedule $record) => ! $record->canAssign(auth()->user()?->isAdmin())),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function liturgicalBadgeClasses(?string $liturgicalColor): string
    {
        return match ($liturgicalColor) {
            'hijau' =>
            '[&_.fi-badge]:!bg-green-100 [&_.fi-badge]:!text-green-800 dark:[&_.fi-badge]:!bg-green-950/40 dark:[&_.fi-badge]:!text-green-200',
            'merah' =>
            '[&_.fi-badge]:!bg-red-100 [&_.fi-badge]:!text-red-800 dark:[&_.fi-badge]:!bg-red-950/40 dark:[&_.fi-badge]:!text-red-200',
            'putih' =>
            '[&_.fi-badge]:!bg-gray-100 [&_.fi-badge]:!text-gray-800 dark:[&_.fi-badge]:!bg-gray-900 dark:[&_.fi-badge]:!text-gray-200',
            'merah muda' =>
            '[&_.fi-badge]:!bg-pink-100 [&_.fi-badge]:!text-pink-800 dark:[&_.fi-badge]:!bg-pink-950/40 dark:[&_.fi-badge]:!text-pink-200',
            'ungu' =>
            '[&_.fi-badge]:!bg-purple-100 [&_.fi-badge]:!text-purple-800 dark:[&_.fi-badge]:!bg-purple-950/40 dark:[&_.fi-badge]:!text-purple-200',
            default => '',
        };
    }
}
