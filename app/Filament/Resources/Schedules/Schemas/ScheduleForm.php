<?php

namespace App\Filament\Resources\Schedules\Schemas;

use Closure;
use App\Models\Schedule;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                Textarea::make('description')
                    ->columnSpanFull(),
                DatePicker::make('scheduled_date')
                    ->required(),
                TimePicker::make('start_time')
                    ->seconds(false)
                    ->required()
                    ->unique(
                        table: 'schedules',
                        column: 'start_time',
                        ignoreRecord: true,
                        modifyRuleUsing: function (Unique $rule, Get $get): Unique {
                            $date = $get('scheduled_date');
                            $locationId = $get('location_id');

                            if (! $date || ! $locationId) {
                                return $rule;
                            }

                            return $rule->where(fn ($query) => $query
                                ->where('scheduled_date', $date)
                                ->where('location_id', $locationId));
                        },
                    )
                    ->validationMessages([
                        'unique' => 'A schedule already exists at this time for the selected location.',
                    ]),
                Select::make('location_id')
                    ->relationship('location', 'name')
                    ->required(),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'locked' => 'Locked',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->default('draft'),
                TextInput::make('required_personnel')
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                        $limit = (int) $state;
                        $selected = (array) $get('user_id');

                        if ($limit > 0 && count($selected) > $limit) {
                            $set('user_id', array_slice($selected, 0, $limit));
                        }
                    }),
                Select::make('liturgical_color')
                    ->label('Liturgical Color')
                    ->options([
                        'hijau' => 'Hijau',
                        'merah' => 'Merah',
                        'putih' => 'Putih',
                        'merah muda' => 'Merah muda',
                        'ungu' => 'Ungu',
                    ])
                    ->searchable()
                    ->nullable(),
                Select::make('user_id')
                    ->multiple()
                    ->relationship('users', 'name')
                    ->searchable()
                    ->required()
                    ->live()
                    ->rule(function (Get $get) {
                        return function (string $attribute, $value, Closure $fail) use ($get) {
                            $requiredPersonnel = (int) $get('required_personnel');

                            if (! is_array($value)) {
                                return;
                            }

                            if ($requiredPersonnel > 0 && count($value) > $requiredPersonnel) {
                                $fail("You can assign at most {$requiredPersonnel} personnel to this schedule.");
                            }
                        };
                    }),
            ])->columns(2);
    }

}
