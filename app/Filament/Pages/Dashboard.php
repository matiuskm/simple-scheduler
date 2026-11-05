<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dasbor';

    protected static ?string $title = 'Dasbor';

    public function getHeading(): string
    {
        return '';
    }
}
