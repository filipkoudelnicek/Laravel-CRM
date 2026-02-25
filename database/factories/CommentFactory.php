<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'body'      => $this->faker->paragraph(),
            'task_id'   => Task::factory(),
            'user_id'   => User::factory(),
            'parent_id' => null,
        ];
    }
}
