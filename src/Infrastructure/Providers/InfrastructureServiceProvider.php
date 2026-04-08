<?php

declare(strict_types=1);

namespace Infrastructure\Providers;

use Domain\Project\Repositories\ProjectRepositoryContract;
use Domain\Sprint\Repositories\SprintRepositoryContract;
use Domain\Task\Repositories\TaskRepositoryContract;
use Domain\Tenant\Repositories\TenantRepositoryContract;
use Domain\User\Repositories\UserRepositoryContract;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Project\Persistence\Repositories\ProjectRepository;
use Infrastructure\Sprint\Persistence\Repositories\SprintRepository;
use Infrastructure\Task\Persistence\Repositories\TaskRepository;
use Infrastructure\Tenant\Persistence\Repositories\TenantRepository;
use Infrastructure\Tenancy\RequestTenantContext;
use Infrastructure\User\Persistence\Repositories\UserRepository;
use Shared\Tenancy\TenantContext;

class InfrastructureServiceProvider extends ServiceProvider
{
    public $singletons = [
        ProjectRepositoryContract::class => ProjectRepository::class,
        TenantRepositoryContract::class => TenantRepository::class,
        TenantContext::class => RequestTenantContext::class,
        UserRepositoryContract::class => UserRepository::class,
        SprintRepositoryContract::class => SprintRepository::class,
        TaskRepositoryContract::class => TaskRepository::class,
    ];

    public function register(): void {}

    public function boot(): void
    {
        // Infrastructure-specific bootstrapping
    }


    /**
     * Register the bindings specified in the singletons array.
     */
    protected function registerSingletons(): void
    {
        foreach ($this->singletons as $interface => $implementation) {
            $this->app->singleton($interface, $implementation);
        }
    }
}
