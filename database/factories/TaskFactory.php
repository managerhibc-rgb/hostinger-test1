<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['todo', 'in_progress', 'review', 'done']);

        return [
            'project_id' => Project::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'status' => $status,
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'assigned_to' => $this->faker->boolean(80) ? User::factory() : null,
            'created_by' => User::factory(),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'completed_at' => $status === 'done' ? $this->faker->dateTimeBetween('-1 week', 'now') : null,
        ];
    }
}
