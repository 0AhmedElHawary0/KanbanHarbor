<?php

declare(strict_types=1);

namespace Application\Tenant\Data;

use Spatie\LaravelData\Data;

class CreateTenantData extends Data
{
    public function __construct(
        public string $name,
    ) {}
}
