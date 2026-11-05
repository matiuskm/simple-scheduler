<?php

namespace App\Filament\Widgets;

use App\Models\Schedule;
use Filament\Widgets\Widget;

class WelcomeHero extends Widget
{
    protected static ?int $sort = -10;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.welcome-hero';

    protected function getViewData(): array
    {
        $user = auth()->user();

        return [
            'isAdmin' => $user?->isAdmin() ?? false,
            'firstName' => explode(' ', trim($user?->name ?? ''))[0],
            'today' => now('Asia/Jakarta')->locale('id')->translatedFormat('l, j F Y'),
            'upcoming' => Schedule::upcomingVisible()->count(),
            'mine' => Schedule::upcomingVisible()->whereHas('users', fn ($q) => $q->where('users.id', $user?->id))->count(),
            'open' => Schedule::upcomingVisible()->needingPersonnel()->count(),
        ];
    }
}
