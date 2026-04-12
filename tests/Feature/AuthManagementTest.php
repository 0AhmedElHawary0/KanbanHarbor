<?php

declare(strict_types=1);

use Application\User\Events\UserRegistered;
use Domain\User\Entities\User;
use Domain\User\Enums\UserStatus;
use Illuminate\Support\Facades\Event;

it('registers a user via auth register endpoint', function (): void {
    /** @var \Tests\TestCase $this */

    Event::fake([UserRegistered::class]);

    $response = $this->postJson('/api/auth/register', [
        'name' => 'Ahmed Hawary',
        'email' => 'ahmed.hawary@example.com',
        'password' => 'secret123',
    ]);

    $response->assertCreated()
        ->assertJsonPath('message', 'User registered successfully')
        ->assertJsonPath('data.email', 'ahmed.hawary@example.com')
        ->assertJsonPath('data.status', UserStatus::Active->value);

    $this->assertDatabaseHas('users', [
        'email' => 'ahmed.hawary@example.com',
        'status' => UserStatus::Active->value,
    ]);

    Event::assertDispatched(UserRegistered::class);
});

it('logs in a user and returns an access token', function (): void {
    /** @var \Tests\TestCase $this */

    User::factory()->create([
        'email' => 'login.user@example.com',
        'password' => 'secret123',
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'login.user@example.com',
        'password' => 'secret123',
        'device_name' => 'feature-test',
    ]);

    $response->assertOk()
        ->assertJsonPath('message', 'Login Successful')
        ->assertJsonPath('data.token_type', 'Bearer');

    expect((string) $response->json('data.access_token'))->not->toBe('');
});

it('rejects login when user email is not verified', function (): void {
    /** @var \Tests\TestCase $this */

    User::factory()->unverified()->create([
        'email' => 'not.verified@example.com',
        'password' => 'secret123',
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'not.verified@example.com',
        'password' => 'secret123',
        'device_name' => 'feature-test',
    ]);

    $response->assertUnprocessable()
        ->assertJsonPath('errors.email.0', 'Email is not verified. Please verify your email before logging in.');
});

it('logs out current token by default', function (): void {
    /** @var \Tests\TestCase $this */

    $user = User::factory()->create();

    $plainTextToken = $user->createToken('feature-test-current')->plainTextToken;
    $tokenId = (int) $user->tokens()->firstOrFail()->id;

    $response = $this->withHeader('Authorization', 'Bearer ' . $plainTextToken)
        ->postJson('/api/auth/logout');

    $response->assertOk()
        ->assertJsonPath('message', 'Logout successful')
        ->assertJsonPath('data.revoked_token_count', 1);

    $this->assertDatabaseMissing('personal_access_tokens', [
        'id' => $tokenId,
    ]);
});

it('logs out all user tokens when all_devices is true', function (): void {
    /** @var \Tests\TestCase $this */

    $user = User::factory()->create();

    $tokenA = $user->createToken('feature-test-a')->plainTextToken;
    $user->createToken('feature-test-b');

    $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
        ->postJson('/api/auth/logout', [
            'all_devices' => true,
        ]);

    $response->assertOk()
        ->assertJsonPath('message', 'Logout successful')
        ->assertJsonPath('data.revoked_token_count', 2);

    $this->assertSame(0, $user->fresh()->tokens()->count());
});
