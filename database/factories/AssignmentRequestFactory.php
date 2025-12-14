<?php

namespace Database\Factories;

use App\Models\AssignmentRequest;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssignmentRequest>
 */
class AssignmentRequestFactory extends Factory
{
    protected $model = AssignmentRequest::class;

    public function definition(): array
    {
        return [
            'schedule_id' => Schedule::factory(),
            'user_id' => User::factory(),
            'status' => AssignmentRequest::STATUS_REQUESTED,
            'reason' => $this->faker->optional()->sentence(),
        ];
    }
}
