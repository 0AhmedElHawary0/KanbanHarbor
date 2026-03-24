<?php

declare(strict_types=1);

namespace Presentation\UserManagement\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\User\Commands\LoginUserCommand;
use Application\User\Commands\LogoutUserCommand;
use Application\User\Commands\RegisterUserCommand;
use Application\User\Data\UserData;
use Domain\User\Enums\UserStatus;
use Illuminate\Http\JsonResponse;
use Presentation\UserManagement\Requests\RegisterUserRequest;
use Presentation\UserManagement\Requests\LoginUserRequest;
use Presentation\UserManagement\Requests\LogoutUserRequest;
use Symfony\Component\HttpFoundation\Response;

final class AuthController
{
    public function __construct(private readonly CommandBusContract $commandBus) {}

    public function register(RegisterUserRequest $request): JsonResponse
    {
        $registrationStatus = UserStatus::tryFrom(
            strtolower((string) config('auth.registration_default_status', UserStatus::Active->value)),
        ) ?? UserStatus::Active;

        $userData = UserData::from([
            ...$request->validated(),
            'status' => $registrationStatus,
        ]);

        $registeredUser = $this->commandBus->dispatch(new RegisterUserCommand($userData));

        return response()->json([
            'message' => 'User registered successfully',
            'data' => $registeredUser,
        ], Response::HTTP_CREATED);
    }


    public function login(LoginUserRequest $request): JsonResponse
    {
        $result = $this->commandBus->dispatch(
            new LoginUserCommand(
                email: (string) $request->input('email'),
                password: (string) $request->input('password'),
                device_name: (string) $request->input('device_name', 'web')
            ),
        );

        return response()->json([
            'message' => 'Login Successful',
            'data' => $result,
        ], Response::HTTP_OK);
    }

    public function logout(LogoutUserRequest $request): JsonResponse
    {
        $user = $request->user();

        abort_if($user === null, Response::HTTP_UNAUTHORIZED);

        $result = $this->commandBus->dispatch(
            new LogoutUserCommand(
                user_id: (int) $user->id,
                current_token_id: $user->currentAccessToken()?->id,
                all_devices: (bool) $request->boolean('all_devices')
            ),
        );

        return response()->json([
            'message' => 'Logout successful',
            'data' => $result,
        ], Response::HTTP_OK);
    }
}
