<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('event'));
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:200'],
            'is_public' => ['boolean'],
            'tag' => ['nullable', 'string', 'in:Atelier,Information'],
            'foot_type' => ['nullable', 'string', 'in:places_limitees,entree_libre'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'title.max' => 'Le titre ne peut pas dépasser 150 caractères.',
            'starts_at.required' => 'La date de début est obligatoire.',
            'starts_at.date' => 'La date de début est invalide.',
            'ends_at.date' => 'La date de fin est invalide.',
            'ends_at.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
        ];
    }
}
