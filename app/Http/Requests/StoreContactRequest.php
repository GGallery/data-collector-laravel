<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // true per autorizzare la richiesta
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('contacts', 'email')
                    ->where(function ($query) {
                        return $query->where('platform_prefix', $this->input('platform_prefix'));
                    })
            ],
            'password' => 'nullable|string|min:6',
            'platform_prefix' => 'required|exists:api_tokens_prefixes,prefix_token'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */

    public function messages(): array
    {
        return [
            'email.unique' => 'Email già esistente per questa piattaforma',
            'platform_prefix.exists' => 'La piattaforma fornita non esiste'
        ];
    }
}
