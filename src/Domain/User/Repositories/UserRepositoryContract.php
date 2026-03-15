<?php

declare(strict_types=1);

namespace Domain\User\Repositories;

use Application\User\Data\UserData;
use Domain\User\Entities\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryContract
{
    public function save(UserData $data, int $tenantId): string;

    public function findByEmail(string $email, int $tenantId): ?User;

    public function findById(int $id, int $tenantId): ?User;

    public function getAllUsers(int $tenantId): Collection;

    public function update(int $id, UserData $data, int $tenantId): bool;
}
