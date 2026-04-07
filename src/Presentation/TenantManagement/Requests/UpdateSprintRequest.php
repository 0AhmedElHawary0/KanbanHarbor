<?php

declare(strict_types=1);

namespace Presentation\TenantManagement\Requests;

use Domain\Sprint\Enums\SprintStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

final class UpdateSprintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'min:3', 'max:120'],
            'goal' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'status' => ['sometimes', new Enum(SprintStatus::class)],
            'starts_at' => ['sometimes', 'nullable', Rule::date()->format('Y-m-d')],
            'ends_at' => ['sometimes', 'nullable', Rule::date()->format('Y-m-d')],
        ];
    }
}
