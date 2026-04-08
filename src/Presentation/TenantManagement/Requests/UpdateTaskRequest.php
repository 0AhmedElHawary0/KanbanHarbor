<?php

declare(strict_types=1);

namespace Presentation\TenantManagement\Requests;

use Domain\Task\Enums\TaskPriority;
use Domain\Task\Enums\TaskStatus;
use Domain\Task\Enums\TaskType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

final class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'min:3', 'max:120'],
            'description' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'type' => ['sometimes', 'nullable', new Enum(TaskType::class)],
            'status' => ['sometimes', 'nullable', new Enum(TaskStatus::class)],
            'priority' => ['sometimes', 'nullable', new Enum(TaskPriority::class)],
            'assignee_id' => ['sometimes', 'nullable', 'int'],
            'story_points' => ['sometimes', 'nullable', 'int'],
            'due_date' => ['sometimes', 'nullable', Rule::date()->format('Y-m-d')],
        ];
    }
}
