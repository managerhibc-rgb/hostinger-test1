<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Label;
use App\Models\Project;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Manager user
        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $manager->assignRole('manager');

        // Member user
        $member = User::firstOrCreate(
            ['email' => 'member@example.com'],
            [
                'name' => 'Member User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $member->assignRole('member');

        // Additional random users
        $users = User::factory(5)->create();
        foreach ($users as $user) {
            $user->assignRole('member');
        }
        $allUsers = User::all();

        // Labels
        $labels = Label::factory(8)->create();

        // Projects
        Project::factory(5)
            ->create(['owner_id' => $allUsers->random()->id])
            ->each(function (Project $project) use ($allUsers, $labels) {
                // Tasks per project
                Task::factory(rand(3, 8))
                    ->create([
                        'project_id' => $project->id,
                        'created_by' => $allUsers->random()->id,
                        'assigned_to' => $allUsers->random()->id,
                    ])
                    ->each(function (Task $task) use ($allUsers, $labels) {
                        // Attach random labels
                        $task->labels()->attach($labels->random(rand(0, 3))->pluck('id'));

                        // Subtasks
                        Subtask::factory(rand(1, 4))->create(['task_id' => $task->id]);

                        // Comments
                        Comment::factory(rand(0, 5))->create([
                            'task_id' => $task->id,
                            'user_id' => $allUsers->random()->id,
                        ]);
                    });
            });

        // Activity logs for a few tasks
        Task::inRandomOrder()->limit(10)->get()->each(function (Task $task) use ($allUsers) {
            $task->activityLogs()->create([
                'user_id' => $allUsers->random()->id,
                'action' => 'created',
                'description' => 'Task created.',
                'metadata' => ['status' => $task->status],
            ]);
        });

        // Notifications
        $allUsers->random(5)->each(function (User $user) use ($allUsers) {
            $task = Task::inRandomOrder()->first();
            $user->notifications()->create([
                'type' => 'task_assigned',
                'title' => 'New task assigned',
                'message' => "You have been assigned to task: {$task->title}",
                'notifiable_id' => $task->id,
                'notifiable_type' => Task::class,
            ]);
        });
    }
}
