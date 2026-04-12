<?php

declare(strict_types=1);

namespace Presentation\UserManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResendVerificationEmailRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => filled($this->input('email')) ? strtolower(trim((string) $this->input('email'))) : null,
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['nullable', 'email'],
        ];
    }
}
