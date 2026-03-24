<?php

declare(strict_types=1);

namespace Presentation\UserManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogoutUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'all_devices' => ['nullable', 'boolean']
        ];
    }
}
