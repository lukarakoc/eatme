<?php

namespace App\Http\Requests;

use App\Rules\NumericRule;
use App\Rules\QuantityRule;
use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'notes' => ['nullable', 'max:5000'],
            'groceries' => ['required', 'array', new QuantityRule, new NumericRule],
            'groceries.*' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'notes.max' => 'Bilješka može sadržati maksimalno 5000 karaktera!',
            'groceries.required' => 'Morate odabrati najmanje jednu namirnicu!',
            'groceries.*.required' => 'Morate odabrati najmanje jednu namirnicu!'
        ];
    }
}
