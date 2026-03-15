<?php

declare(strict_types=1);

namespace Presentation\UserManagement\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\User\Commands\CreateUserCommand;
use Application\User\Contracts\UserServiceContract;
use Application\User\Data\UserData;
use Application\User\Data\UsersListData;
use Application\User\Queries\GetUserByEmailQuery;
use Application\User\Queries\GetUserByIdQuery;
use Illuminate\Support\Collection;
use Presentation\Controller;
use Presentation\UserManagement\Requests\UserFormRequest;
use Shared\Tenancy\TenantContext;

class UserController extends Controller
{
    public function __construct(
        protected CommandBusContract $commandBus,
        protected QueryBusContract $queryBus,
        protected UserServiceContract $userService,
        protected TenantContext $tenantContext,
    ) {}

    public function store(UserFormRequest $request): UsersListData
    {
        $userData = UserData::from($request->validated());
        $tenantId = $this->tenantContext->tenantId();

        $email = $this->commandBus->dispatch(
            new CreateUserCommand($userData, $tenantId),
        );

        $user = $this->queryBus->ask(
            new GetUserByEmailQuery($email, $tenantId),
        );

        return UsersListData::from($user);
    }

    public function update(int $id, UserFormRequest $request): UsersListData
    {
        $userData = UserData::from($request->validated());
        $tenantId = $this->tenantContext->tenantId();

        $this->userService->update($id, $userData, $tenantId);

        $user = $this->queryBus->ask(new GetUserByEmailQuery($request->email, $tenantId));

        return UsersListData::from($user);
    }

    public function index(): Collection
    {
        $users = $this->userService->index($this->tenantContext->tenantId());

        return UsersListData::collect($users);
    }

    public function show(int $id): UsersListData
    {
        $user = $this->queryBus->ask(new GetUserByIdQuery($id, $this->tenantContext->tenantId()));

        abort_if($user === null, 404);

        return UsersListData::from($user);
    }
}
