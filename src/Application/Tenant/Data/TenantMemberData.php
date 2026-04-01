<?php

declare(strict_types=1);

namespace Application\Tenant\Data;

use Carbon\Carbon;
use Domain\Tenant\Entities\Tenant;
use Domain\User\Entities\User;
use Domain\User\Enums\UserRole;
use Domain\User\Enums\UserStatus;
use Spatie\LaravelData\Data;

class TenantMemberData extends Data
{
    public function __construct(
        public int $id,
        public int $tenant_id,
        public string $name,
        public string $email,
        public UserStatus $status,
        public UserRole $role,
        public Carbon $created_at,
        public Carbon $updated_at,
    ) {}

    public static function fromModel(User $user): self
    {
        /** @var Tenant|null $tenant */
        $tenant = $user->relationLoaded('tenants') ? $user->tenants->first() : null;
        $pivot = $user->pivot ?? $tenant?->pivot;

        return new self(
            id: (int) $user->id,
            tenant_id: (int) ($pivot?->tenant_id ?? $tenant?->id ?? 0),
            name: (string) $user->name,
            email: (string) $user->email,
            status: $user->status,
            role: UserRole::from((string) ($pivot?->role ?? UserRole::Member->value)),
            created_at: $user->created_at,
            updated_at: $user->updated_at,
        );
    }
}
