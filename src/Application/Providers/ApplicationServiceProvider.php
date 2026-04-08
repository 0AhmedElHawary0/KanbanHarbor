<?php

declare(strict_types=1);

namespace Application\Providers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\Bus\IlluminateCommandBus;
use Application\Bus\IlluminateQueryBus;
use Application\Project\CommandHandlers\CreateProjectCommandHandler;
use Application\Project\CommandHandlers\ArchiveProjectCommandHandler;
use Application\Sprint\CommandHandlers\StoreSprintCommandHandler;
use Application\Sprint\QueryHandlers\ListProjectSprintsQueryHandler;
use Application\Project\Commands\CreateProjectCommand;
use Application\Project\Commands\ArchiveProjectCommand;
use Application\Project\QueryHandlers\GetProjectByIdQueryHandler;
use Application\Project\QueryHandlers\ListTenantProjectsQueryHandler;
use Application\Project\Queries\GetProjectByIdQuery;
use Application\Project\Queries\ListTenantProjectsQuery;
use Application\Sprint\CommandHandlers\ArchiveSprintCommandHandler;
use Application\Sprint\CommandHandlers\UpdateSprintCommandHandler;
use Application\Sprint\Commands\ArchiveSprintCommand;
use Application\Sprint\Commands\StoreSprintCommand;
use Application\Sprint\Commands\UpdateSprintCommand;
use Application\Sprint\Queries\GetSprintByIdQuery;
use Application\Sprint\Queries\ListProjectSprintsQuery;
use Application\Sprint\QueryHandlers\GetSprintByIdQueryHandler;
use Application\Task\CommandHandlers\StoreTaskCommandHandler;
use Application\Task\CommandHandlers\UpdateTaskCommandHandler;
use Application\Task\Commands\StoreTaskCommand;
use Application\Task\Commands\UpdateTaskCommand;
use Application\Task\Queries\GetTaskByIdQuery;
use Application\Task\Queries\ListSprintTasksQuery;
use Application\Task\QueryHandlers\GetTaskByIdQueryHandler;
use Application\Task\QueryHandlers\ListSprintTasksQueryHandler;
use Application\Tenant\CommandHandlers\AddTenantMemberCommandHandler;
use Application\Tenant\CommandHandlers\AssignTenantMemberRoleCommandHandler;
use Application\Tenant\CommandHandlers\CreateTenantCommandHandler;
use Application\Tenant\Commands\AddTenantMemberCommand;
use Application\Tenant\Commands\AssignTenantMemberRoleCommand;
use Application\Tenant\Commands\CreateTenantCommand;
use Application\Tenant\QueryHandlers\GetTenantByIdQueryHandler;
use Application\Tenant\QueryHandlers\GetTenantMemberByIdQueryHandler;
use Application\Tenant\QueryHandlers\ListTenantMembersQueryHandler;
use Application\Tenant\Queries\GetTenantByIdQuery;
use Application\Tenant\Queries\GetTenantMemberByIdQuery;
use Application\Tenant\Queries\ListTenantMembersQuery;
use Application\User\CommandHandlers\LoginUserCommandHandler;
use Application\User\CommandHandlers\LogoutUserCommandHandler;
use Application\User\CommandHandlers\RegisterUserCommandHandler;
use Application\User\CommandHandlers\UpdateUserCommandHandler;
use Application\User\Commands\LoginUserCommand;
use Application\User\Commands\LogoutUserCommand;
use Application\User\Commands\RegisterUserCommand;
use Application\User\Commands\UpdateUserCommand;
use Application\User\QueryHandlers\GetUserByEmailQueryHandler;
use Application\User\QueryHandlers\GetUserByIdQueryHandler;
use Application\User\QueryHandlers\ListUsersQueryHandler;
use Application\User\Queries\GetUserByEmailQuery;
use Application\User\Queries\GetUserByIdQuery;
use Application\User\Queries\ListUsersQuery;
use Illuminate\Support\ServiceProvider;

class ApplicationServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [];

    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        CommandBusContract::class => IlluminateCommandBus::class,
        QueryBusContract::class => IlluminateQueryBus::class,
    ];

    public function register(): void {}

    public function boot(): void
    {
        $this->registerCommandHandlers();
        $this->registerQueryHandlers();
    }

    protected function registerCommandHandlers(): void
    {
        $commandBus = app(CommandBusContract::class);
        $commandBus->register([

            /* Project */
            CreateProjectCommand::class => CreateProjectCommandHandler::class,
            ArchiveProjectCommand::class => ArchiveProjectCommandHandler::class,

            /* Sprint */
            StoreSprintCommand::class => StoreSprintCommandHandler::class,
            UpdateSprintCommand::class => UpdateSprintCommandHandler::class,
            ArchiveSprintCommand::class => ArchiveSprintCommandHandler::class,

            /* Tenant */
            CreateTenantCommand::class => CreateTenantCommandHandler::class,
            AddTenantMemberCommand::class => AddTenantMemberCommandHandler::class,
            AssignTenantMemberRoleCommand::class => AssignTenantMemberRoleCommandHandler::class,

            /* User */
            RegisterUserCommand::class => RegisterUserCommandHandler::class,
            UpdateUserCommand::class => UpdateUserCommandHandler::class,
            LoginUserCommand::class => LoginUserCommandHandler::class,
            LogoutUserCommand::class => LogoutUserCommandHandler::class,

            /* Task */
            StoreTaskCommand::class => StoreTaskCommandHandler::class,
            UpdateTaskCommand::class => UpdateTaskCommandHandler::class,
        ]);
    }

    protected function registerQueryHandlers(): void
    {
        $queryBus = app(QueryBusContract::class);
        $queryBus->register([

            /* Project */
            GetProjectByIdQuery::class => GetProjectByIdQueryHandler::class,
            ListTenantProjectsQuery::class => ListTenantProjectsQueryHandler::class,
            ListProjectSprintsQuery::class => ListProjectSprintsQueryHandler::class,

            /* Tenant */
            GetTenantByIdQuery::class => GetTenantByIdQueryHandler::class,
            GetTenantMemberByIdQuery::class => GetTenantMemberByIdQueryHandler::class,
            ListTenantMembersQuery::class => ListTenantMembersQueryHandler::class,

            /* User */
            GetUserByEmailQuery::class => GetUserByEmailQueryHandler::class,
            GetUserByIdQuery::class => GetUserByIdQueryHandler::class,
            ListUsersQuery::class => ListUsersQueryHandler::class,

            /* Sprint */
            GetSprintByIdQuery::class => GetSprintByIdQueryHandler::class,

            /* Task */
            GetTaskByIdQuery::class => GetTaskByIdQueryHandler::class,
            ListSprintTasksQuery::class => ListSprintTasksQueryHandler::class,
        ]);
    }
}
