<?php

declare(strict_types=1);

namespace Presentation\UserManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyEmailRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
            'hash' => $this->route('hash'),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:users,id'],
            'hash' => ['required', 'string'],
        ];
    }
}
