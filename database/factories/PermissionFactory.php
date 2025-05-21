<?php

/* @var $factory Factory */

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->name(),
            'display_name' => $this->faker->name(),
            'description' => $this->faker->text(),
        ];
    }
}
