<?php

declare(strict_types=1);

namespace Presentation\TenantManagement\Requests;

use Domain\User\Enums\UserRole;
use Domain\User\Enums\UserStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class AddTenantMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'max:20'],
            'status' => ['required', new Enum(UserStatus::class)],
            'role' => ['required', new Enum(UserRole::class)],
        ];
    }
}
