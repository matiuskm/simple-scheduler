<?php

namespace App\Filament\Resources\Schedules\Tables;

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
                    ->badge(),
                TextColumn::make('assigned_count')
                    ->label('Assigned'),
                TextColumn::make('required_personnel')
                    ->label('Quota'),
            ])
            ->filters([
                Filter::make('upcoming')
                    ->label('Upcoming only')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->upcoming()),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
