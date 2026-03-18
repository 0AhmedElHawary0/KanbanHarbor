<?php

declare(strict_types=1);

namespace Presentation\UserManagement\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\User\Commands\RegisterUserCommand;
use Application\User\Data\UserData;
use Illuminate\Http\JsonResponse;
use Presentation\UserManagement\Requests\RegisterUserRequest;
use Symfony\Component\HttpFoundation\Response;

final class AuthController
{
    public function __construct(private readonly CommandBusContract $commandBus) {}

    public function register(RegisterUserRequest $request): JsonResponse
    {
        $userData = UserData::from($request->validated());

        $registeredUser = $this->commandBus->dispatch(new RegisterUserCommand($userData));

        return response()->json([
            'message' => 'User registered successfully',
            'data' => $registeredUser,
        ], Response::HTTP_CREATED);
    }
}
