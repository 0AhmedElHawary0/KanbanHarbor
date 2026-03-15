<?php

declare(strict_types=1);

namespace Application\User\QueryHandlers;

use Application\Bus\QueryHandler;
use Application\User\Queries\GetUserByIdQuery;
use Domain\User\Repositories\UserRepositoryContract;

class GetUserByIdQueryHandler extends QueryHandler
{
    public function __construct(
        protected readonly UserRepositoryContract $repository,
    ) {}

    public function handle(GetUserByIdQuery $query): ?object
    {
        return $this->repository->findById(
            $query->getId(),
            $query->getTenantId(),
        );
    }
}
