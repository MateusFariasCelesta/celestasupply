<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'password'       => ['nullable', 'string', 'min:8', 'confirmed'],
            'role'           => ['required', 'in:requester,buyer,admin'],
            'whatsapp_phone' => ['nullable', 'string', 'max:20'],
            'isActive'       => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('isActive')) {
            $this->merge(['isActive' => $this->boolean('isActive')]);
        }
    }

    public function attributes(): array
    {
        return [
            'name'           => 'nome',
            'email'          => 'e-mail',
            'password'       => 'senha',
            'role'           => 'perfil',
            'whatsapp_phone' => 'WhatsApp',
        ];
    }
}
