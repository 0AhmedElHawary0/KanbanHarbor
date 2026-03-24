<?php

declare(strict_types=1);

namespace Domain\User\Repositories;

use Application\User\Data\RegisteredUserData;
use Application\User\Data\UserData;
use Domain\User\Entities\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryContract
{
    public function register(UserData $data): RegisteredUserData;

    public function findByEmail(string $email, int $tenantId): ?User;

    public function findById(int $id, int $tenantId): ?User;

    public function getAllUsers(int $tenantId): Collection;

    public function update(int $id, UserData $data, int $tenantId): bool;

    public function findByEmailForAuth(string $email): ?User;

    public function createApiToken(User $user, string $deviceName): string;

    public function findByIdForAuth(int $userId): ?User;

    public function revokeCurrentToken(User $user, ?int $tokenId): bool;

    public function revokeAllTokens(User $user): int;
}
