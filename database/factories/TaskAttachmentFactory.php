<?php

/* @var $factory Factory */

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskAttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $task = Task::factory()->create();

        return [
            'task_id' => $task->id,
        ];
    }
}
