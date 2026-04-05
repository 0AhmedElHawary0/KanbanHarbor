<?php

declare(strict_types=1);

namespace Presentation\TenantManagement\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\Sprint\Commands\StoreSprintCommand;
use Application\Sprint\Data\StoreSprintData;
use Aws\Api\ErrorParser\JsonRpcErrorParser;
use Illuminate\Http\JsonResponse;
use Presentation\Controller;
use Shared\Tenancy\TenantContext;
use Symfony\Component\HttpFoundation\Response;
use Presentation\TenantManagement\Requests\StoreSprintRequest;

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
}
