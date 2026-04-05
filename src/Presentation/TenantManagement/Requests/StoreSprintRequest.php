<?php

declare(strict_types=1);

namespace Presentation\TenantManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreSprintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'goal' => ['nullable', 'string', 'max:2000'],
            'starts_at' => [Rule::date()->format('Y-m-d')],
            'ends_at' => [Rule::date()->format('Y-m-d')],
        ];
    }
}
