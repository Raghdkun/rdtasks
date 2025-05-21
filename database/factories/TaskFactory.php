<?php

/* @var $factory Factory */

namespace Database\Factories;

use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $project = Project::factory()->create();

        $dueDate = date('Y-m-d H:i:s', strtotime('+ 4hours'));

        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->text(),
            'project_id' => $project->id,
            'due_date' => $dueDate,
            'status' => Task::$status['STATUS_ACTIVE'],
            'task_number' => $this->faker->unique()->randomDigitNotNull(),
        ];
    }

    public function tag()
    {
        return $this->state(function () {
            $tag = Tag::factory()->create();

            return [
                'tags' => [$tag->id],
            ];
        });
    }

    public function assignees()
    {
        return $this->state(function () {
            $assignees = User::factory()->count(2)->create();

            return [
                'assignees' => [$assignees[0]->id, $assignees[1]->id],
            ];
        });
    }
}
