<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCostCenterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'id'       => ['required', 'string', 'max:20', 'unique:cost_centers,id', 'regex:/^\d+(\.\d+)?$/'],
            'name'     => ['required', 'string', 'max:255'],
            'isActive' => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'id'   => 'código',
            'name' => 'nome',
        ];
    }

    public function messages(): array
    {
        return [
            'id.regex'  => 'O código deve conter apenas números (ex: 1, 100, 110.01).',
            'id.unique' => 'Já existe um centro de custo com esse código.',
        ];
    }
}
