<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroceryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return $this->is('admin/groceries/store') ? $this->createRules() : $this->updateRules();
    }

    public function createRules(): array
    {
        return [
            'name' => 'required|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,svg,gif|max:2048',
            'unit_price' => 'required|numeric',
            'is_product' => 'required'
        ];
    }

    public function updateRules(): array
    {
        return [
            'name' => 'required|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,svg,gif|max:2048',
            'unit_price' => 'required|numeric',
            'is_product' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Morate unijeti naziv namirnice',
            'name.max' => 'Naziv namirnice može sadržati najviše 255 karaktera',
            'unit_price.required' => 'Morate unijeti cijenu namirnice',
            'is_product.required' => 'Morate unijeti da li je unos izlazni proizvod',
            'image.mimes' => 'Slika mora biti u validnom formatu',
            'image.max' => 'Maksimalna dozvoljena veličina slike je 2MB',
            'image.image' => 'Neuspješno dodavanje slike. Provjerite maksimalnu veličinu (2MB) ili format'
        ];
    }
}
