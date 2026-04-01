<?php

declare(strict_types=1);

namespace Presentation\TenantManagement\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\Tenant\Commands\AddTenantMemberCommand;
use Application\Tenant\Commands\AssignTenantMemberRoleCommand;
use Application\Tenant\Commands\CreateTenantCommand;
use Application\Tenant\Data\CreateTenantData;
use Application\Tenant\Data\TenantData;
use Application\Tenant\Data\TenantMemberCreateData;
use Application\Tenant\Data\TenantMemberData;
use Application\Tenant\Queries\GetTenantByIdQuery;
use Application\Tenant\Queries\GetTenantMemberByIdQuery;
use Application\Tenant\Queries\ListTenantMembersQuery;
use Illuminate\Http\JsonResponse;
use Presentation\Controller;
use Presentation\TenantManagement\Requests\AddTenantMemberRequest;
use Presentation\TenantManagement\Requests\CreateTenantRequest;
use Presentation\TenantManagement\Requests\UpdateTenantMemberRoleRequest;
use Shared\Tenancy\TenantContext;
use Symfony\Component\HttpFoundation\Response;

final class TenantController extends Controller
{
    public function __construct(
        private readonly CommandBusContract $commandBus,
        private readonly QueryBusContract $queryBus,
        private readonly TenantContext $tenantContext,
    ) {}

    public function store(CreateTenantRequest $request): JsonResponse
    {
        $user = $request->user();

        abort_if($user === null, Response::HTTP_UNAUTHORIZED);

        $tenantId = $this->commandBus->dispatch(
            new CreateTenantCommand(
                CreateTenantData::from($request->validated()),
                (int) $user->id
            ),
        );

        $tenant = $this->queryBus->ask(new GetTenantByIdQuery($tenantId));

        abort_if($tenant === null, Response::HTTP_NOT_FOUND);

        return response()->json([
            'data' => TenantData::from($tenant),
        ], Response::HTTP_CREATED);
    }

    public function storeMember(int $tenantId, AddTenantMemberRequest $request): JsonResponse
    {
        $resolvedTenantId = $this->tenantContext->tenantId();

        abort_if($tenantId !== $resolvedTenantId, Response::HTTP_BAD_REQUEST);

        $memberId = $this->commandBus->dispatch(
            new AddTenantMemberCommand(
                $resolvedTenantId,
                TenantMemberCreateData::from($request->validated()),
            ),
        );

        $member = $this->queryBus->ask(new GetTenantMemberByIdQuery($resolvedTenantId, $memberId));

        abort_if($member === null, Response::HTTP_NOT_FOUND);

        return response()->json([
            'data' => TenantMemberData::from($member),
        ], Response::HTTP_CREATED);
    }

    public function members(int $tenantId): JsonResponse
    {
        $resolvedTenantId = $this->tenantContext->tenantId();

        abort_if($tenantId !== $resolvedTenantId, Response::HTTP_BAD_REQUEST);

        $members = $this->queryBus->ask(new ListTenantMembersQuery($resolvedTenantId));

        return response()->json(
            $members
                ->map(fn($member) => TenantMemberData::fromModel($member)->toArray())
                ->values()
                ->all()
        );
    }

    public function updateMemberRole(int $tenantId, int $userId, UpdateTenantMemberRoleRequest $request): JsonResponse
    {
        $resolvedTenantId = $this->tenantContext->tenantId();

        abort_if($tenantId !== $resolvedTenantId, Response::HTTP_BAD_REQUEST);

        $memberId = $this->commandBus->dispatch(
            new AssignTenantMemberRoleCommand(
                $resolvedTenantId,
                $userId,
                $request->enum('role', \Domain\User\Enums\UserRole::class),
            ),
        );

        abort_if($memberId === null, Response::HTTP_NOT_FOUND);

        $member = $this->queryBus->ask(new GetTenantMemberByIdQuery($resolvedTenantId, $memberId));

        abort_if($member === null, Response::HTTP_NOT_FOUND);

        return response()->json([
            'data' => TenantMemberData::from($member),
        ]);
    }
}
