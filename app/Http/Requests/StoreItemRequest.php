<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isBuyerOrAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:items,name'],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'nome'];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Já existe um item com esse nome no catálogo.',
        ];
    }
}
