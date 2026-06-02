<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplyRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isActive;
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'cost_center_id' => ['required', 'exists:cost_centers,id'],
            'urgency'        => ['required', 'in:low,medium,high'],
            'notes'          => ['nullable', 'string'],
            'action'         => ['required', 'in:draft,submit'],
            'items'          => ['required', 'array', 'min:1'],
            'items.*.item_id'  => ['required', 'string'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit'     => ['required', 'string', 'max:50'],
            'items.*.notes'    => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title'          => 'título',
            'cost_center_id' => 'centro de custo',
            'urgency'        => 'urgência',
            'items'          => 'itens',
            'items.*.item_id'  => 'item',
            'items.*.quantity' => 'quantidade',
        ];
    }
}
