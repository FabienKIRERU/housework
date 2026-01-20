<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
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
            // Infos Réservation
            'service_id' => 'required|exists:services,id',
            'intervention_date' => 'required|date|after:now',
            'address' => 'required|string|min:5',
            
            // Infos Client
            'client_name' => 'required|string|min:2',
            'client_firstname' => 'required|string|min:2',
            'client_email' => 'required|email',
            'client_phone' => 'required|string|min:8',
        ];
    }
}
