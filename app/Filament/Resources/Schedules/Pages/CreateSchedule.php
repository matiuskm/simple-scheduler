<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\ScheduleResource;
use Illuminate\Validation\ValidationException;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;

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

    /**
     * Catch unique slot violations and surface a friendly validation error.
     */
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            return parent::handleRecordCreation($data);
        } catch (QueryException $e) {
            $message = $e->getMessage();
            $sqlState = $e->getCode();

            $isUniqueSlot = $sqlState === '23000'
                || Str::contains($message, ['unique_schedule_slot', 'UNIQUE constraint failed: schedules.scheduled_date, schedules.start_time, schedules.location_id']);

            if ($isUniqueSlot) {
                Notification::make()
                    ->title('Schedule conflict')
                    ->body('A schedule already exists at this time for the selected location.')
                    ->danger()
                    ->send();

                throw ValidationException::withMessages([
                    'start_time' => 'A schedule already exists at this time for the selected location.',
                ]);
            }

            throw $e;
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
