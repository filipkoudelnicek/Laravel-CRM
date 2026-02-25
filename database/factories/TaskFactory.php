<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'       => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'status'      => $this->faker->randomElement(['todo', 'in_progress', 'review', 'done']),
            'priority'    => $this->faker->randomElement(['low', 'medium', 'high']),
            'due_date'    => $this->faker->optional()->dateTimeBetween('now', '+3 months'),
            'project_id'  => Project::factory(),
            'created_by'  => User::factory(),
        ];
    }
}
