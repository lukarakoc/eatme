<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
            'amount' => 'required|numeric',
            'type' => 'required|numeric',
            'notes' => 'nullable|max:5000'
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => 'Morate unijeti iznos troška',
            'amount.numeric' => 'Iznos troška mora biti u formatu broja',
            'type.required' => 'Morate odabrati tip troška',
            'type.numeric' => 'Tip troška mora biti u formatu broja',
            'notes.max' => 'Detalji o trošku mogu sadržati maksimalno 5000 karaktera'
        ];
    }
}
