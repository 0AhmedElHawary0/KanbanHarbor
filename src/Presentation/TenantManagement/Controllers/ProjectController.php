<?php

declare(strict_types=1);

namespace Presentation\TenantManagement\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\Project\Commands\CreateProjectCommand;
use Application\Project\Data\CreateProjectData;
use Application\Project\Data\ProjectData;
use Application\Project\Queries\GetProjectByIdQuery;
use Application\Project\Queries\ListTenantProjectsQuery;
use Illuminate\Http\JsonResponse;
use Presentation\Controller;
use Presentation\TenantManagement\Requests\CreateProjectRequest;
use Shared\Tenancy\TenantContext;
use Symfony\Component\HttpFoundation\Response;

final class ProjectController extends Controller
{
    public function __construct(
        private readonly CommandBusContract $commandBus,
        private readonly QueryBusContract $queryBus,
        private readonly TenantContext $tenantContext,
    ) {}

    public function store(int $tenantId, CreateProjectRequest $request): JsonResponse
    {
        $resolvedTenantId = $this->tenantContext->tenantId();

        abort_if($tenantId !== $resolvedTenantId, Response::HTTP_BAD_REQUEST);

        $projectId = $this->commandBus->dispatch(
            new CreateProjectCommand(
                $resolvedTenantId,
                CreateProjectData::from($request->validated()),
            ),
        );

        $project = $this->queryBus->ask(new GetProjectByIdQuery($resolvedTenantId, $projectId));

        abort_if($project === null, Response::HTTP_NOT_FOUND);

        return response()->json([
            'data' => ProjectData::from($project),
        ], Response::HTTP_CREATED);
    }

    public function view(int $tenantId): JsonResponse
    {
        $resolvedTenantId = $this->tenantContext->tenantId();

        abort_if($tenantId !== $resolvedTenantId, Response::HTTP_BAD_REQUEST);

        $projects = $this->queryBus->ask(new ListTenantProjectsQuery($resolvedTenantId));

        return response()->json([
            'data' => ProjectData::collect($projects),
        ], Response::HTTP_OK);
    }
}
