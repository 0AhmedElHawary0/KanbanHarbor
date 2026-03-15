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

final class TenantRepository implements TenantRepositoryContract
{
    public function create(CreateTenantData $data): Tenant
    {
        return Tenant::query()->create([
            'name' => $data->name,
            'slug' => $this->generateUniqueSlug($data->name),
        ]);
    }

    public function findById(int $tenantId): ?Tenant
    {
        return Tenant::query()->find($tenantId);
    }

    public function addMember(int $tenantId, TenantMemberCreateData $data): User
    {
        return User::query()->create([
            'tenant_id' => $tenantId,
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password,
            'status' => $data->status,
            'role' => $data->role,
        ]);
    }

    public function findMemberById(int $tenantId, int $userId): ?User
    {
        return User::query()
            ->where('tenant_id', $tenantId)
            ->whereKey($userId)
            ->first();
    }

    public function listMembers(int $tenantId): Collection
    {
        return User::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('id')
            ->get();
    }

    public function assignMemberRole(int $tenantId, int $userId, UserRole $role): ?User
    {
        $member = $this->findMemberById($tenantId, $userId);

        if ($member === null) {
            return null;
        }

        $member->update([
            'role' => $role,
        ]);

        return $member->refresh();
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
