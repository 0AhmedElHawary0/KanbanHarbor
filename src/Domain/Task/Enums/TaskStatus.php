<?php

declare(strict_types=1);

namespace Domain\Task\Enums;

use Domain\Common\Traits\EnumValues;

enum TaskStatus : string 
{
    use EnumValues;

    case ToDo = 'todo';
    case InProgress = 'in_progress';
    case InReview = 'in_review';
    case Done = 'done';
    case Blocked = 'blocked';
}