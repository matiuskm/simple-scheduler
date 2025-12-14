<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\ScheduleResource;
use Illuminate\Validation\ValidationException;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSchedule extends EditRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $required = (int) ($data['required_personnel'] ?? 0);
        $selected = (array) ($data['user_id'] ?? []);

        if ($required > 0 && count($selected) > $required) {
            throw ValidationException::withMessages([
                'user_id' => "You can assign at most {$required} personnel to this schedule.",
            ]);
        }

        return $data;
    }
}
