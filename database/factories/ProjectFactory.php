<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->catchPhrase(),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['active', 'archived', 'completed']),
            'owner_id' => User::factory(),
            'start_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'due_date' => $this->faker->dateTimeBetween('now', '+3 months'),
        ];
    }
}
