<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\ScheduleResource;
use App\Models\User;
use App\Notifications\ScheduleAssignmentConfirmed;
use App\Notifications\ScheduleForcedRelease;
use Illuminate\Validation\ValidationException;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditSchedule extends EditRecord
{
    protected static string $resource = ScheduleResource::class;

    /**
     * @var array<int>
     */
    protected array $previousUserIds = [];

    protected function getHeaderActions(): array
    {
        return [
            Action::make('google_calendar')
                ->label('Google Calendar')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn () => ScheduleResource::googleCalendarUrl($this->record))
                ->openUrlInNewTab(),
            Action::make('download_ics')
                ->label('Download .ics')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn () => ScheduleResource::scheduleIcsUrl($this->record)),
            DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $this->previousUserIds = $this->record
            ->users()
            ->pluck('users.id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    protected function afterSave(): void
    {
        $this->record->load('users');

        $currentUserIds = $this->record
            ->users
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $added = array_values(array_diff($currentUserIds, $this->previousUserIds));
        $removed = array_values(array_diff($this->previousUserIds, $currentUserIds));

        foreach ($added as $userId) {
            $this->record->logAssignmentAdded($userId);

            if ((bool) config('scheduler.email_notifications', true)) {
                User::find($userId)?->notify(new ScheduleAssignmentConfirmed($this->record));
            }
        }

        $actor = auth()->user();

        foreach ($removed as $userId) {
            $this->record->logAssignmentRemoved($userId);

            if ((bool) config('scheduler.email_notifications', true)) {
                User::find($userId)?->notify(new ScheduleForcedRelease($this->record, $actor));
            }
        }
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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
