<?php

declare(strict_types=1);

namespace Domain\Sprint\Enums;

use Domain\Common\Traits\EnumValues;

enum SprintStatus : string 
{
    use EnumValues;

    case Active = 'active';
    case Archived = 'archived';
}