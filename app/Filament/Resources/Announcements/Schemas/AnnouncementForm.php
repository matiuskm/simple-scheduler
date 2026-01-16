<?php

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('message')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                DatePicker::make('start_at')
                    ->required(),
                DatePicker::make('end_at')
                    ->required()
                    ->after('start_at'),
                TextInput::make('background_color')
                    ->required()
                    ->helperText('Hex color, e.g. #1E88E5')
                    ->rule('regex:/^#([0-9a-fA-F]{6}|[0-9a-fA-F]{3})$/'),
                Toggle::make('is_enabled')
                    ->default(true),
            ]);
    }
}
