<?php

declare(strict_types=1);

namespace Domain\Task\Enums;

use Domain\Common\Traits\EnumValues;

enum TaskPriority : string 
{
    use EnumValues;

    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Urgent = 'urgent';

}