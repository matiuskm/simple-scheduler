<?php

namespace App\Filament\Resources\Schedules\RelationManagers;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $relatedResource = UserResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->schema([
                        Select::make('recordId')
                            ->label('Users')
                            ->multiple()
                            ->preload(true)
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                return User::query()
                                    ->where('name', 'like', "%{$search}%")
                                    ->orderBy('name')
                                    ->limit(50)
                                    ->pluck('name', 'id');
                            })
                            ->getOptionLabelsUsing(fn (array $values) => User::whereIn('id', $values)->pluck('name', 'id'))
                            ->getOptionLabelUsing(fn ($value) => User::find($value)?->name),
                    ])
                    ->using(function (RelationManager $livewire, array $data): void {
                        $schedule = $livewire->getOwnerRecord();

                        $schedule->assertCanAssign(auth()->user()?->isAdmin());

                        $recordIds = (array) ($data['recordId'] ?? []);
                        $selectedCount = count($recordIds);
                        $currentCount = $schedule->users()->count();
                        $required = (int) $schedule->required_personnel;

                        if ($required > 0 && $currentCount + $selectedCount > $required) {
                            throw ValidationException::withMessages([
                                'recordIds' => "You can assign at most {$required} personnel to this schedule.",
                            ]);
                        }

                        $schedule->users()->attach($recordIds, [
                            'assigned_by' => auth()->id(),
                        ]);

                        foreach ($recordIds as $userId) {
                            $schedule->logAssignmentAdded((int) $userId);
                        }
                    }),
            ])
            ->recordActions([
                DetachAction::make()
                    ->using(function (RelationManager $livewire, $record): void {
                        $schedule = $livewire->getOwnerRecord();
                        $isAdmin = auth()->user()?->isAdmin();

                        if ($schedule->is_locked && ! $isAdmin) {
                            throw ValidationException::withMessages([
                                'recordIds' => 'Schedule is locked before start; only admins may modify assignments.',
                            ]);
                        }

                        $schedule->users()->detach($record->getKey());
                        $schedule->logAssignmentRemoved((int) $record->getKey());
                    }),
            ]);
    }
}
