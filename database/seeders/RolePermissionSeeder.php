<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // User management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Role & permission management
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',

            // Project management
            'projects.view',
            'projects.create',
            'projects.edit',
            'projects.delete',

            // Task management
            'tasks.view',
            'tasks.create',
            'tasks.edit',
            'tasks.delete',

            // Subtask management
            'subtasks.view',
            'subtasks.create',
            'subtasks.edit',
            'subtasks.delete',

            // Label management
            'labels.view',
            'labels.create',
            'labels.edit',
            'labels.delete',

            // Comment management
            'comments.view',
            'comments.create',
            'comments.edit',
            'comments.delete',

            // Attachment management
            'attachments.view',
            'attachments.create',
            'attachments.edit',
            'attachments.delete',

            // Activity log management
            'activity_logs.view',
            'activity_logs.create',
            'activity_logs.edit',
            'activity_logs.delete',

            // Notification management
            'notifications.view',
            'notifications.create',
            'notifications.edit',
            'notifications.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::where('guard_name', 'web')->get());

        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $managerRole->givePermissionTo(Permission::where('guard_name', 'web')
            ->whereNotIn('name', [
                'roles.create', 'roles.edit', 'roles.delete',
                'permissions.create', 'permissions.edit', 'permissions.delete',
                'users.delete',
            ])
            ->get());

        $memberRole = Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
        $memberRole->givePermissionTo([
            'projects.view',
            'tasks.view', 'tasks.create', 'tasks.edit',
            'subtasks.view', 'subtasks.create', 'subtasks.edit',
            'labels.view',
            'comments.view', 'comments.create', 'comments.edit',
            'attachments.view', 'attachments.create',
            'activity_logs.view',
            'notifications.view', 'notifications.create', 'notifications.edit',
        ]);

        // API roles
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'api']);
        Role::firstOrCreate(['name' => 'member', 'guard_name' => 'api']);
    }
}
