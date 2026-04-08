<?php

declare(strict_types=1);

namespace Presentation\TenantManagement\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\Task\Commands\StoreTaskCommand;
use Application\Task\Data\StoreTaskData;
use Illuminate\Http\JsonResponse;
use Presentation\Controller;
use Presentation\TenantManagement\Requests\StoreTaskRequest;
use Shared\Tenancy\TenantContext;
use Symfony\Component\HttpFoundation\Response;

final class TaskController extends Controller
{
    public function __construct(
        private readonly CommandBusContract $commandBus,
        private readonly QueryBusContract $queryBus,
        private readonly TenantContext $tenantContext,
    ) {}

    public function store(int $tenantId, int $sprintId, StoreTaskRequest $request): JsonResponse
    {
        $resolvedTenantId = $this->tenantContext->tenantId();
        abort_if($tenantId !== $resolvedTenantId, Response::HTTP_BAD_REQUEST);

        $task = $this->commandBus->dispatch(
            new StoreTaskCommand(
                $resolvedTenantId,
                $sprintId,
                StoreTaskData::from($request->validated())
            )
        );

        return response()->json(
            [
                'data' => $task,
            ],
            Response::HTTP_CREATED
        );
    }
}
