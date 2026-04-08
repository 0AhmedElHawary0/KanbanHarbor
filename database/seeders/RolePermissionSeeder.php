<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        // All actions for authorization 
        $permissions = [
            'tenant.view',
            'tenant.update',
            'member.view',
            'member.invite',
            'member.role.update',
            'project.view',
            'project.create',
            'project.update',
            'project.delete',
            'project.archive',
            'sprint.create',
            'sprint.view',
            'sprint.delete',
            'sprint.update',
            'sprint.archive',
            'task.create',
            'task.view',
            'task.update',
        ];

        // Create permissions if the don't exist yet
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $rolePermissions = [
            'owner' => $permissions,
            'admin' => [
                'tenant.view',
                'tenant.update',
                'member.view',
                'member.invite',
                'member.role.update',
                'project.view',
                'project.create',
                'project.update',
                'project.archive',
                'sprint.create',
                'sprint.view',
                'sprint.update',
                'sprint.archive',
                'task.create',
                'task.view',
                'task.update',



            ],
            'member' => [
                'tenant.view',
                'member.view',
                'project.view',
                'task.view',
            ],
        ];

        foreach ($rolePermissions as $roleName => $assignedPermissions) {
            $roles = Role::query()->where('name', $roleName)->get();

            if ($roles->isEmpty()) {
                $roles = collect([Role::findOrCreate($roleName, 'web')]);
            }

            $roles->each(function (Role $role) use ($assignedPermissions): void {
                $role->syncPermissions($assignedPermissions);
            });
        }
    }
}
