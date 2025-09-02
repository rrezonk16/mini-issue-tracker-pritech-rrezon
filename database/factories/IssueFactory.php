<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;

class IssueFactory extends Factory
{
    protected $model = \App\Models\Issue::class;

    public function definition()
    {
        $statuses = ['open', 'in_progress', 'closed'];
        $priorities = ['low', 'medium', 'high'];

        return [
            'project_id' => Project::factory(),
            'title' => $this->faker->sentence(5),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement($statuses),
            'priority' => $this->faker->randomElement($priorities),
            'due_date' => $this->faker->dateTimeBetween('now', '+2 months')->format('Y-m-d'),
        ];
    }
}
