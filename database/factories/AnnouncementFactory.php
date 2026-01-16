<?php

namespace Database\Factories;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    public function definition(): array
    {
        return [
            'message' => $this->faker->paragraph(),
            'start_at' => now()->subHour(),
            'end_at' => now()->addHour(),
            'background_color' => '#1E88E5',
            'is_enabled' => true,
        ];
    }
}
