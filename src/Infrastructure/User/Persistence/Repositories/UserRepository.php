<?php

declare(strict_types=1);

namespace Infrastructure\User\Persistence\Repositories;

use Application\User\Data\UserData;
use Domain\User\Entities\User;
use Domain\User\Repositories\UserRepositoryContract;
use Illuminate\Database\Eloquent\Collection;

final class UserRepository implements UserRepositoryContract
{
    public function save(UserData $data, int $tenantId): string
    {
        $user = User::create([
            ...$data->all(),
            'tenant_id' => $tenantId,
        ]);

        return $user->email;
    }

    public function findByEmail(string $email, int $tenantId): ?User
    {
        $user = User::query()
            ->where('tenant_id', $tenantId)
            ->where('email', $email)
            ->first();

        return $user ?? null;
    }

    public function findById(int $id, int $tenantId): ?User
    {
        $user = User::query()
            ->where('tenant_id', $tenantId)
            ->where('id', $id)
            ->first();

        return $user ?? null;
    }

    public function getAllUsers(int $tenantId): Collection
    {
        return User::query()
            ->where('tenant_id', $tenantId)
            ->get();
    }

    public function update(int $id, UserData $data, int $tenantId): bool
    {
        $user = User::query()
            ->where('tenant_id', $tenantId)
            ->findOrFail($id);

        return $user->update($data->all());
    }
}
