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
        ];

        // Create permissions if the don't exist yet
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Create roles if they don't exist yet
        $owner = Role::findOrCreate('owner', 'web');
        $admin = Role::findOrCreate('admin', 'web');
        $member = Role::findOrCreate('member', 'web');

        // Owner has all permissions
        $owner->syncPermissions($permissions);

        // Admin
        $admin->syncPermissions([
            'tenant.view',
            'tenant.update',
            'member.view',
            'member.invite',
            'member.role.update',
            'project.view',
            'project.create',
            'project.update',
            'project.archive',
        ]);

        //Member
        $member->syncPermissions([
            'tenant.view',
            'member.view',
            'project.view',
        ]);
    }
}
