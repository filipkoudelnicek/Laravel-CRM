<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => $this->faker->company(),
            'email'      => $this->faker->unique()->companyEmail(),
            'phone'      => $this->faker->phoneNumber(),
            'company'    => $this->faker->company(),
            'address'    => $this->faker->address(),
            'notes'      => $this->faker->sentence(),
            'created_by' => User::factory(),
        ];
    }
}
