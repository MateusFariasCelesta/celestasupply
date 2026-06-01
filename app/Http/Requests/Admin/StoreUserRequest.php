<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
            'role'           => ['required', 'in:requester,buyer,admin'],
            'whatsapp_phone' => ['nullable', 'string', 'max:20'],
        ];
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
