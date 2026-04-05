<?php

declare(strict_types=1);

namespace Domain\Task\Enums;

use Domain\Common\Traits\EnumValues;

enum TaskType : string 
{
    use EnumValues;

    case Feature = 'feature';
    case Bug = 'bug';
    case Chore = 'chore';
    case Spike = 'spike';

}