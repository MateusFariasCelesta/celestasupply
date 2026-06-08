<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isBuyerOrAdmin();
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255', Rule::unique('items', 'name')->ignore($this->route('item'))],
            'isActive' => ['sometimes', 'boolean'],
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
        return ['name' => 'nome'];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Já existe um item com esse nome no catálogo.',
        ];
    }
}
