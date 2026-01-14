<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHousewokerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $houseworkerId = $this->route('houseworker');

        return [
            'name'      => 'sometimes|string|max:30',
            'firstname' => 'sometimes|string|max:50',
            'phone'     => 'sometimes|string|max:20',
            // On ignore l'email de l'utilisateur actuel
            'email'     => ['sometimes', 'email', Rule::unique('users')->ignore($houseworkerId)],
            'password'  => 'nullable|string|min:8',
        ];
    }
}
