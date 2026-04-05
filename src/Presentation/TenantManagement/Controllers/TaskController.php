<?php

declare(strict_types=1);

namespace Presentation\TenantManagement\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Aws\Api\ErrorParser\JsonRpcErrorParser;
use Illuminate\Http\JsonResponse;
use Presentation\Controller;
use Shared\Tenancy\TenantContext;
use Symfony\Component\HttpFoundation\Response;

final class TaskController extends Controller
{
    public function __construct(
        private readonly CommandBusContract $commandBus,
        private readonly QueryBusContract $queryBus,
        private readonly TenantContext $tenantContext,
    ) {}



}
