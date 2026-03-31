<?php

declare(strict_types=1);

namespace Infrastructure\Tenant\Persistence\Repositories;

use Application\Tenant\Data\CreateTenantData;
use Application\Tenant\Data\TenantMemberCreateData;
use Domain\Tenant\Entities\Tenant;
use Domain\Tenant\Repositories\TenantRepositoryContract;
use Domain\User\Entities\User;
use Domain\User\Enums\UserRole;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class TenantRepository implements TenantRepositoryContract
{
    public function create(CreateTenantData $data, int $ownerId): Tenant
    {
        $tenant =  Tenant::query()->create([
            'name' => $data->name,
            'slug' => $this->generateUniqueSlug($data->name),
        ]);

        $owner = User::query()->find($ownerId);

        if ($owner == null) {
            throw new \RuntimeException('Owner user not found.');
        }

        $owner->tenants()->syncWithoutDetaching([
            (int) $tenant->id => ['role' => UserRole::Owner->value],
        ]);

        $this->syncTenantScopedRole($owner, (int) $tenant->id, UserRole::Owner);

        return $tenant;
    }

    public function findById(int $tenantId): ?Tenant
    {
        return Tenant::query()->find($tenantId);
    }

    public function addMember(int $tenantId, TenantMemberCreateData $data): User
    {
        $user = User::query()->where('email', $data->email)->first();

        if ($user === null) {
            throw new \RuntimeException('Invited user was not found.');
        }

        $isExistingMember = $user->tenants()
            ->where('tenant_id', $tenantId)
            ->exists();

        if ($isExistingMember) {
            $user->tenants()->updateExistingPivot($tenantId, ['role' => $data->role->value]);
        } else {
            $user->tenants()->attach($tenantId, ['role' => $data->role->value]);
        }

        $this->syncTenantScopedRole($user, $tenantId, $data->role);

        return $user->load('tenants');
    }

    public function findMemberById(int $tenantId, int $userId): ?User
    {
        return User::query()
            ->whereHas('tenants', function ($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId);
            })
            ->whereKey($userId)
            ->first();
    }

    public function listMembers(int $tenantId): Collection
    {
        return Tenant::query()
            ->whereKey($tenantId)
            ->first()
            ->users()
            ->orderBy('users.id')
            ->get();
    }

    public function assignMemberRole(int $tenantId, int $userId, UserRole $role): ?User
    {
        $user = $this->findMemberById($tenantId, $userId);

        if ($user === null) {
            return null;
        }

        $user->tenants()->updateExistingPivot($tenantId, ['role' => $role->value]);

        $this->syncTenantScopedRole($user, $tenantId, $role);

        return $user->load('tenants');
    }


    private function syncTenantScopedRole(User $user, int $tenantId, UserRole $role): void
    {
        setPermissionsTeamId($tenantId);

        try {
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            $tenantRole = Role::query()->firstOrCreate([
                'name' => $role->value,
                'guard_name' => 'web',
                (string) config('permission.column_names.team_foreign_key', 'team_id') => $tenantId,
            ]);

            $permissions = collect($this->permissionNamesForRole($role))
                ->map(fn(string $permissionName) => Permission::findOrCreate($permissionName, 'web'));

            $tenantRole->syncPermissions($permissions);
            $user->syncRoles([$tenantRole]);
        } finally {
            setPermissionsTeamId(null);
        }
    }

    /**
     * @return list<string>
     */
    private function permissionNamesForRole(UserRole $role): array
    {
        return match ($role) {
            UserRole::Owner => [
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
            ],
            UserRole::Admin => [
                'tenant.view',
                'tenant.update',
                'member.view',
                'member.invite',
                'member.role.update',
                'project.view',
                'project.create',
                'project.update',
                'project.archive',
            ],
            UserRole::Member => [
                'tenant.view',
                'member.view',
                'project.view',
            ],
        };
    }

    private function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $suffix = 1;

        while (Tenant::query()->where('slug', $slug)->exists()) {
            $slug = sprintf('%s-%d', $baseSlug, $suffix);
            $suffix++;
        }

        return $slug;
    }
}
