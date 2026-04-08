<?php

declare(strict_types=1);

namespace Presentation\TenantManagement\Requests;

use Domain\Task\Enums\TaskPriority;
use Domain\Task\Enums\TaskStatus;
use Domain\Task\Enums\TaskType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

final class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'type' => ['nullable', new Enum(TaskType::class)],
            'status' => ['nullable', new Enum(TaskStatus::class)],
            'priority' => ['nullable', new Enum(TaskPriority::class)],
            'assignee_id' => ['nullable', 'int'],
            'story_points' => ['nullable', 'int'],
            'due_date' => ['nullable', Rule::date()->format('Y-m-d')],
        ];
    }
}
