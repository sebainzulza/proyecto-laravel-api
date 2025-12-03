<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $serverId = $this->route('server');

        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('servers', 'name')->ignore($serverId)],
            'ip_address' => ['sometimes', 'string', 'ip'],
            'status' => ['nullable', 'boolean'],
        ];
    }
}
