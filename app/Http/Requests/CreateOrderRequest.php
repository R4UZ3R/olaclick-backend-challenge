<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'client_name.required' => 'El nombre del cliente es obligatorio',
            'items.required' => 'La orden debe contener al menos un item',
            'items.*.description.required' => 'La descripción del item es obligatoria',
            'items.*.quantity.required' => 'La cantidad es obligatoria',
            'items.*.quantity.min' => 'La cantidad debe ser como mínimo 1',
            'items.*.unit_price.required' => 'El precio unitario es obligatorio',
            'items.*.unit_price.min' => 'El precio no puede ser negativo',
        ];
    }
}