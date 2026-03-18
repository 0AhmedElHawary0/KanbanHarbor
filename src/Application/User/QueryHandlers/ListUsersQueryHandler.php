<?php

declare(strict_types=1);

namespace Application\User\QueryHandlers;

use Application\Bus\QueryHandler;
use Application\User\Queries\ListUsersQuery;
use Domain\User\Repositories\UserRepositoryContract;
use Illuminate\Database\Eloquent\Collection;

final class ListUsersQueryHandler extends QueryHandler
{
    public function __construct(private readonly UserRepositoryContract $userRepository) {}

    public function handle(ListUsersQuery $query): Collection
    {
        return $this->userRepository->getAllUsers($query->getTenantId());
    }
}
