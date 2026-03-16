<?php

declare(strict_types=1);

namespace Domain\Project\Enums;

use Domain\Common\Traits\EnumValues;

enum ProjectStatus: string
{
    use EnumValues;

    case Active = 'active';
    case Archived = 'archived';
}
