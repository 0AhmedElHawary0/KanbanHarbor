<?php

declare(strict_types=1);

namespace Presentation\TenantManagement\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\Task\Commands\StoreTaskCommand;
use Application\Task\Commands\UpdateTaskCommand;
use Application\Task\Data\StoreTaskData;
use Application\Task\Data\TaskData;
use Application\Task\Data\UpdateTaskData;
use Application\Task\Queries\GetTaskByIdQuery;
use Application\Task\Queries\ListSprintTasksQuery;
use Illuminate\Http\JsonResponse;
use Presentation\Controller;
use Presentation\TenantManagement\Requests\StoreTaskRequest;
use Presentation\TenantManagement\Requests\UpdateTaskRequest;
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

    public function index(int $tenantId, int $sprintId): JsonResponse
    {
        $resolvedTenantId = $this->tenantContext->tenantId();
        abort_if($tenantId !== $resolvedTenantId, Response::HTTP_BAD_REQUEST);

        $tasks = $this->queryBus->ask(new ListSprintTasksQuery(
            $resolvedTenantId,
            $sprintId,
        ));

        return response()->json(
            [
                'data' => TaskData::collect($tasks),
            ],
            Response::HTTP_OK
        );
    }

    public function show(int $tenantId, int $taskId): JsonResponse
    {
        $resolvedTenantId = $this->tenantContext->tenantId();
        abort_if($tenantId !== $resolvedTenantId, Response::HTTP_BAD_REQUEST);

        $task = $this->queryBus->ask(new GetTaskByIdQuery($resolvedTenantId, $taskId));

        return response()->json(
            [
                'data' => TaskData::from($task),
            ],
            Response::HTTP_OK
        );
    }

    public function update(int $tenantId, int $taskId, UpdateTaskRequest $request): JsonResponse
    {
        $resolvedTenantId = $this->tenantContext->tenantId();
        abort_if($tenantId !== $resolvedTenantId, Response::HTTP_BAD_REQUEST);

        $task = $this->commandBus->dispatch(
            new UpdateTaskCommand(
                $resolvedTenantId,
                $taskId,
                UpdateTaskData::from($request->validated()),
            )
        );

        return response()->json(
            [
                'data' => $task
            ],
            Response::HTTP_OK
        );
    }
}
