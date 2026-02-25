<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Crypt;

class PasswordEntryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'              => $this->faker->company() . ' Login',
            'username'           => $this->faker->userName(),
            'password_encrypted' => Crypt::encryptString($this->faker->password()),
            'url'                => $this->faker->optional()->url(),
            'notes'              => $this->faker->optional()->sentence(),
            'client_id'          => null,
            'project_id'         => null,
            'created_by'         => User::factory(),
        ];
    }
}
