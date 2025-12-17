<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Schedule>
 */
class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    public function definition(): array
    {
        $startsAt = Carbon::now()->addDays(2)->setTime(9, 0, 0);

        return [
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->sentence(8),
            'scheduled_date' => $startsAt->toDateString(),
            'start_time' => $startsAt->format('H:i:s'),
            'end_time' => $startsAt->copy()->addHour()->format('H:i:s'),
            'location_id' => Location::factory(),
            'status' => Schedule::STATUS_PUBLISHED,
            'required_personnel' => 2,
            'liturgical_color' => fake()->randomElement(['hijau', 'merah', 'putih', 'merah muda', 'ungu']),
        ];
    }

    public function draft(): self
    {
        return $this->state(fn () => ['status' => Schedule::STATUS_DRAFT]);
    }
}
