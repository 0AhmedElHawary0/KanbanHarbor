<?php

declare(strict_types=1);

namespace Domain\Tenant\Repositories;

use Application\Tenant\Data\CreateTenantData;
use Application\Tenant\Data\TenantMemberCreateData;
use Domain\Tenant\Entities\Tenant;
use Domain\User\Entities\User;
use Domain\User\Enums\UserRole;
use Illuminate\Database\Eloquent\Collection;

interface TenantRepositoryContract
{
    public function create(CreateTenantData $data): Tenant;

    public function findById(int $tenantId): ?Tenant;

    public function addMember(int $tenantId, TenantMemberCreateData $data): User;

    public function findMemberById(int $tenantId, int $userId): ?User;

    public function listMembers(int $tenantId): Collection;

    public function assignMemberRole(int $tenantId, int $userId, UserRole $role): ?User;
}
