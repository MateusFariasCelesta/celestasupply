<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isBuyerOrAdmin();
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'contact'  => ['nullable', 'string', 'max:255'],
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
        return [
            'name'    => 'nome',
            'contact' => 'contato',
        ];
    }
}
