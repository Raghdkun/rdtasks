<?php

/* @var $factory Factory */

namespace Database\Factories;

use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFilterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $report = Report::factory()->create();

        return [
            'param_id' => $this->faker->randomDigit(),
            'report_id' => $report->id,
            'param_type' => $this->faker->word(),
        ];
    }
}
