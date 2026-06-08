<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCostCenterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isBuyerOrAdmin();
    }

    public function rules(): array
    {
        return [
            'id'       => ['required', 'string', 'max:20', Rule::unique('cost_centers')->ignore($this->costCenter)],
            'name'     => ['required', 'string', 'max:255'],
            'isActive' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('isActive')) {
            $this->merge(['isActive' => $this->boolean('isActive')]);
        }
    }

    public function messages(): array
    {
        return [
            'id.unique' => 'Já existe um centro de custo com esse código.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id'   => 'código',
            'name' => 'nome',
        ];
    }
}
