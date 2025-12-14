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
                    ->time(),
                TextColumn::make('location.name')
                    ->label('Location'),
                TextColumn::make('status')
                    ->label('Lifecycle')
                    ->state(fn (Schedule $record) => $record->lifecycle_status)
                    ->badge()
                    ->colors([
                        'gray' => Schedule::STATUS_DRAFT,
                        'success' => [Schedule::STATUS_OPEN, Schedule::STATUS_COMPLETED],
                        'warning' => Schedule::STATUS_FULL,
                        'info' => Schedule::STATUS_PUBLISHED,
                        'danger' => [Schedule::STATUS_LOCKED, Schedule::STATUS_CANCELLED],
                    ]),
                TextColumn::make('capacity')
                    ->getStateUsing(fn (Schedule $record) => "{$record->assigned_count} / {$record->required_personnel}")
                    ->badge()
                    ->color(fn (Schedule $record) => $record->isFull ? 'warning' : 'success'),
            ])
            ->filters([
                Filter::make('upcoming')
                    ->label('Upcoming only')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->upcoming()),
                Filter::make('needs_personnel')
                    ->label('Needs personnel')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->needingPersonnel()),
            ])
            ->recordActions([
                EditAction::make()
                    ->disabled(fn (Schedule $record) => ! $record->canAssign(auth()->user()?->isAdmin())),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
