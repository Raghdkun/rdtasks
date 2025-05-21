<?php

/* @var $factory Factory */

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $client = Client::factory()->create();

        return [
            'name' => $this->faker->name(),
            'prefix' => $this->faker->name(),
            'description' => $this->faker->sentence(),
            'client_id' => $client->id,
        ];
    }
}
