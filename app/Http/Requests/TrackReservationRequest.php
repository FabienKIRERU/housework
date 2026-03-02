<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackReservationRequest extends FormRequest
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
        return [
            'code' => 'required|string',
            
            // "required_without_all" signifie : Si les autres sont vides, alors je suis obligatoire.
            'email' => 'nullable|email|required_without_all:phone',
            'phone' => 'nullable|string|required_without_all:email',
            // 'name' => 'nullable|string|required_without_all:email,phone',
        ];
    }
    public function messages()
    {
        return [
            'email.required_without_all' => 'Veuillez renseigner votre email ou votre téléphone pour identifier votre reservation.',
            // ... tu peux personnaliser les autres
        ];
    }
}
