<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        return $this->is('admin/users/store') ? $this->createRules() : $this->updateRules();
    }

    public function createRules(): array
    {
        return [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|max:32',
            'role_id' => 'required',
        ];
    }

    public function updateRules(): array
    {
        return [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|max:32',
            'role_id' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Molimo Vas unesite ime i prezime',
            'name.max' => 'Ime i prezime može sadržati maksimalno 255 karaktera',
            'email.required' => 'Molimo Vas unesite email',
            'email.email' => 'Email nije u validnom formatu',
            'email.max' => 'Email može sadržati maksimalno 255 karaktera',
            'password.required' => 'Molimo Vas unesite lozinku',
            'password.max' => 'Lozinka može sadržati maksimalno 32 karaktera',
            'role_id.required' => 'Molimo Vas izaberite ulogu'
        ];
    }
}
