<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isBuyerOrAdmin();
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'    => 'nome',
            'contact' => 'contato',
        ];
    }

}
