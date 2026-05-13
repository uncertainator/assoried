<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLabInternalRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'circle_id' => ['required', 'integer', 'exists:circles,id'],
            'lab_service_id' => ['nullable', 'integer', 'exists:lab_services,id'],
            'message' => ['required', 'string', 'max:2000'],
            'desired_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'circle_id.required' => 'Veuillez sélectionner un cercle.',
            'circle_id.exists' => 'Le cercle sélectionné est invalide.',
            'lab_service_id.exists' => 'Le service sélectionné est invalide.',
            'message.required' => 'Le message est obligatoire.',
            'message.max' => 'Le message ne peut pas dépasser 2000 caractères.',
            'desired_date.date' => 'La date souhaitée doit être une date valide.',
            'desired_date.after_or_equal' => 'La date souhaitée ne peut pas être dans le passé.',
        ];
    }
}
