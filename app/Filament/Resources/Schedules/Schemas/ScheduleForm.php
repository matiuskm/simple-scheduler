<?php

namespace App\Filament\Resources\Schedules\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->columnSpanFull(),
                DatePicker::make('scheduled_date')
                    ->required(),
                TimePicker::make('start_time')
                    ->format('H:i')
                    ->required(),
                TimePicker::make('end_time')
                    ->format('H:i'),
                Select::make('location_id')
                    ->relationship('location', 'name')
                    ->searchable()
                    ->required(),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ])
                    ->required()
                    ->default('draft'),
                TextInput::make('required_personnel')
                    ->numeric()
                    ->minValue(1)
                    ->default(1),
                Select::make('user_id')
                    ->multiple()
                    ->relationship('users', 'name')
                    ->searchable()
                    ->required(),
            ])->columns(2);
    }

    protected function mutateFormDataBeforeCreate(array $data): array {
        if ($this->ownerRecord->is_full) {
            abort(422, 'Schedule is already full.');
        }

        $data['assigned_by'] = auth()->id();

        return $data;
    }
}
