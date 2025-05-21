<?php

/* @var $factory Factory */

namespace Database\Factories;

use App\Models\ActivityType;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimeEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create();
        $activityType = ActivityType::factory()->create();
        $task = Task::factory()->create();

        $startTime = date('Y-m-d H:i:s');
        $endTime = date('Y-m-d H:i:s', strtotime($startTime.'+1 hours'));

        return [
            'task_id' => $task->id,
            'activity_type_id' => $activityType->id,
            'user_id' => $user->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration' => $this->faker->randomDigit(),
            'note' => $this->faker->sentence(),
            'entry_type' => TimeEntry::STOPWATCH,
        ];
    }
}
