<?php

namespace Database\Factories;

use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubtaskFactory extends Factory
{
    protected $model = Subtask::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['todo', 'done']);

        return [
            'task_id' => Task::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'status' => $status,
            'due_date' => $this->faker->dateTimeBetween('now', '+2 weeks'),
            'completed_at' => $status === 'done' ? $this->faker->dateTimeBetween('-1 week', 'now') : null,
        ];
    }
}
