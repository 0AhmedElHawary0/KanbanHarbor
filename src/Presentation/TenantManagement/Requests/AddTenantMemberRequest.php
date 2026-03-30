<?php

declare(strict_types=1);

namespace Presentation\TenantManagement\Requests;

use Domain\User\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class AddTenantMemberRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim((string) $this->input('email'))),
        ]);
    }

    public function authorize(): bool
    {
        $user = $this->user();
        if ($user === null) {
            return false;
        }
        return $user->can('member.invite');
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'role' => ['required', new Enum(UserRole::class)],
        ];
    }
}
