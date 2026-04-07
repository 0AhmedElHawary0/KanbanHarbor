<?php

declare(strict_types=1);

namespace Presentation\TenantManagement\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\Project\Queries\GetProjectByIdQuery;
use Application\Sprint\Commands\StoreSprintCommand;
use Application\Sprint\Commands\UpdateSprintCommand;
use Application\Sprint\Data\SprintData;
use Application\Sprint\Data\StoreSprintData;
use Application\Sprint\Queries\GetSprintByIdQuery;
use Application\Sprint\Queries\ListProjectSprintsQuery;
use Illuminate\Http\JsonResponse;
use Presentation\Controller;
use Presentation\TenantManagement\Requests\StoreSprintRequest;
use Presentation\TenantManagement\Requests\UpdateSprintRequest;
use Shared\Tenancy\TenantContext;
use Symfony\Component\HttpFoundation\Response;

final class SprintController extends Controller
{
    public function __construct(
        private readonly CommandBusContract $commandBus,
        private readonly QueryBusContract $queryBus,
        private readonly TenantContext $tenantContext,
    ) {}


    public function store(int $tenantId, int $projectId, StoreSprintRequest $request): JsonResponse
    {
        $resolvedTenantId = $this->tenantContext->tenantId();

        abort_if($tenantId !== $resolvedTenantId, Response::HTTP_BAD_REQUEST);

        $sprint = $this->commandBus->dispatch(
            new StoreSprintCommand(
                $resolvedTenantId,
                $projectId,
                StoreSprintData::from($request->validated())
            ),
        );

        return response()->json([
            'data' => $sprint,

        ], Response::HTTP_CREATED);
    }

    public function index(int $tenantId, int $projectId): JsonResponse
    {
        $resolvedTenantId = $this->tenantContext->tenantId();
        abort_if($tenantId !== $resolvedTenantId, Response::HTTP_BAD_REQUEST);

        $sprints = $this->queryBus->ask(new ListProjectSprintsQuery($resolvedTenantId, $projectId));

        return response()->json([
            'data' => SprintData::collect($sprints),
        ], Response::HTTP_OK);
    }

    public function show(int $tenantId, int $projectId, int $sprintId): JsonResponse
    {
        $resolvedTenantId = $this->tenantContext->tenantId();
        abort_if($tenantId !== $resolvedTenantId, Response::HTTP_BAD_REQUEST);

        $sprint = $this->queryBus->ask(new GetSprintByIdQuery($resolvedTenantId, $projectId, $sprintId));

        return response()->json(
            [
                'data' => SprintData::from($sprint),
            ],
            Response::HTTP_OK
        );
    }

    public function update(int $tenantId, int $projectId, int $sprintId, UpdateSprintRequest $request): JsonResponse
    {
        $resolvedTenantId = $this->tenantContext->tenantId();
        abort_if($tenantId !== $resolvedTenantId, Response::HTTP_BAD_REQUEST);

        $sprint = $this->commandBus->dispatch(
            new UpdateSprintCommand(
                $resolvedTenantId,
                $projectId,
                $sprintId,
                StoreSprintData::from($request->validated())
            )
        );

        return response()->json(
            [
                'data' => SprintData::from($sprint),
            ],
            Response::HTTP_OK
        );
    }
}
