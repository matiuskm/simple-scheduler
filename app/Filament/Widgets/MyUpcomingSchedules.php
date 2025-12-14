<?php

namespace App\Filament\Widgets;

use App\Models\Schedule;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class MyUpcomingSchedules extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'My Upcoming Schedules';

    protected function getTableQuery(): Builder
    {
        $user = auth()->user();

        return Schedule::query()
            ->with('location')
            ->upcomingVisible()
            ->when($user, fn (Builder $query) => $query->whereHas('users', fn ($q) => $q->where('users.id', $user->id)));
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
            ])
            ->recordActions([
                Action::make('release')
                    ->label('Release')
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Schedule $record) {
                        $user = auth()->user();

                        if (! $user) {
                            throw ValidationException::withMessages(['user' => 'You must be logged in.']);
                        }

                        $record->assertCanRelease($user->isAdmin());

                        $record->users()->detach($user->id);
                        $record->logAssignmentRemoved($user->id);
                    }),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }
}
