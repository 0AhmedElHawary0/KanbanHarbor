<?php

declare(strict_types=1);

namespace App\Providers;

use Application\User\Events\UserRegistered;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Infrastructure\User\Listeners\SendEmailVerificationOnUserRegistered;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [];

    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [];

    /**
     * Register any application services.
     */
    public function register(): void {}

    public function boot(): void
    {
        Event::listen(UserRegistered::class, SendEmailVerificationOnUserRegistered::class);
    }
}
