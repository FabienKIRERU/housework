<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReservationRequest extends FormRequest
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
            'houseworker_id' => [
                'nullable', 
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'houseworker'))
            ],
            
            'status' => 'sometimes|in:pending,confirmed,completed,cancelled',
            
            // --- AJOUT : Si le statut est 'confirmed', alors houseworker_id est requis ---
            // (Note: cette règle native de Laravel est bien, mais elle ne vérifie pas 
            // si la ménagère est DEJA en base. C'est pour ça que le Repository est plus puissant.
            // Mais on peut laisser ça pour valider l'input direct).
            // 'houseworker_id' => 'required_if:status,confirmed' <--- On évite ça car ça bloquerait si la ménagère est déjà en base.
            
            'intervention_date' => 'sometimes|date',
            'address' => 'sometimes|string',
        ];
    }
}
