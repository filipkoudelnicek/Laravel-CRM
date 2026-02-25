<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'        => $this->faker->bs(),
            'description' => $this->faker->paragraph(),
            'status'      => $this->faker->randomElement(['planned', 'active', 'on_hold', 'done']),
            'due_date'    => $this->faker->optional()->dateTimeBetween('now', '+6 months'),
            'client_id'   => Client::factory(),
            'created_by'  => User::factory(),
        ];
    }
}
