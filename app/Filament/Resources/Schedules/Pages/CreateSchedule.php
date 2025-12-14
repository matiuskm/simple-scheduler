<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\ScheduleResource;
use Illuminate\Validation\ValidationException;
use Filament\Resources\Pages\CreateRecord;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
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
