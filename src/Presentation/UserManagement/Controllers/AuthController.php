<?php

declare(strict_types=1);

namespace Presentation\UserManagement\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\User\Commands\LoginUserCommand;
use Application\User\Commands\LogoutUserCommand;
use Application\User\Commands\RegisterUserCommand;
use Application\User\Data\UserData;
use Domain\User\Entities\User;
use Domain\User\Enums\UserStatus;
use Illuminate\Http\JsonResponse;
use Presentation\UserManagement\Requests\LoginUserRequest;
use Presentation\UserManagement\Requests\LogoutUserRequest;
use Presentation\UserManagement\Requests\RegisterUserRequest;
use Presentation\UserManagement\Requests\ResendVerificationEmailRequest;
use Presentation\UserManagement\Requests\VerifyEmailRequest;
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
                email: (string) $request->validated('email'),
                password: (string) $request->validated('password'),
                device_name: (string) $request->validated('device_name', 'web')
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

    public function verifyEmail(VerifyEmailRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = User::query()->findOrFail((int) $validated['id']);
        $hash = (string) $validated['hash'];

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json([
                'message' => 'Invalid verification link.',
            ], Response::HTTP_FORBIDDEN);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.',
            ], Response::HTTP_OK);
        }

        $user->markEmailAsVerified();

        return response()->json([
            'message' => 'Email verified successfully.',
        ], Response::HTTP_OK);
    }

    public function resendVerificationEmail(ResendVerificationEmailRequest $request): JsonResponse
    {
        $email = (string) $request->validated('email');

        if ($email !== '') {
            $user = User::query()->where('email', $email)->first();

            if ($user !== null && ! $user->hasVerifiedEmail()) {
                $user->sendEmailVerificationNotification();
            }
        }

        return response()->json([
            'message' => 'If the account exists and is unverified, a verification email has been sent.',
        ], Response::HTTP_OK);
    }
}
