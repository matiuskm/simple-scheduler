<?php

namespace App\Filament\Widgets;

use App\Models\Schedule;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Validation\ValidationException;

class OpenUpcomingSchedules extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Open Upcoming Schedules';

    protected function getTableQuery(): Builder
    {
        $user = auth()->user();

        return Schedule::query()
            ->with('location')
            ->upcomingVisible()
            ->needingPersonnel()
            ->when($user, fn (Builder $query) => $query->whereDoesntHave('users', fn ($q) => $q->where('users.id', $user->id)));
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Schedule')->wrap(),
                TextColumn::make('location.name')->label('Location'),
                TextColumn::make('scheduled_date')->date(),
                TextColumn::make('start_time')->time(),
                TextColumn::make('lifecycle_status')->label('Status')->colors([
                    'success' => Schedule::STATUS_OPEN,
                    'warning' => Schedule::STATUS_FULL,
                ]),
                TextColumn::make('capacity')
                    ->label('Capacity')
                    ->getStateUsing(fn (Schedule $record) => "{$record->assigned_count} / {$record->required_personnel}")
                    ->badge()
                    ->color(fn (Schedule $record) => $record->isFull ? 'warning' : 'success'),
            ])
            ->recordActions([
                Action::make('request')
                    ->label('Request assignment')
                    ->icon('heroicon-o-plus')
                    ->requiresConfirmation()
                    ->action(function (Schedule $record) {
                        $user = auth()->user();

                        if (! $user) {
                            throw ValidationException::withMessages(['user' => 'You must be logged in.']);
                        }

                        $record->assertCanAssign($user->isAdmin());

                        if ($record->users()->where('users.id', $user->id)->exists()) {
                            throw ValidationException::withMessages(['user' => 'You are already assigned.']);
                        }

                        $record->users()->attach($user->id, ['assigned_by' => $user->id]);
                        $record->logAssignmentAdded($user->id);
                    }),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }
}
