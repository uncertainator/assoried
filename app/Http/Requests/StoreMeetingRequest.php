<?php

namespace App\Http\Requests;

use App\Models\Meeting;
use Illuminate\Foundation\Http\FormRequest;

class StoreMeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [Meeting::class, $this->route('circle')]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'location' => ['nullable', 'string', 'max:255'],
            'visio_url' => ['nullable', 'url', 'max:2048'],
            'agenda_items' => ['required', 'array', 'min:1'],
            'agenda_items.*.title' => ['required', 'string', 'max:255'],
            'agenda_items.*.duration_minutes' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'scheduled_at.required' => 'La date et l\'heure sont obligatoires.',
            'scheduled_at.date' => 'La date et l\'heure sont invalides.',
            'scheduled_at.after' => 'La réunion doit être planifiée dans le futur.',
            'duration_minutes.integer' => 'La durée doit être un nombre entier.',
            'duration_minutes.min' => 'La durée doit être d\'au moins 1 minute.',
            'location.max' => 'Le lieu ne peut pas dépasser 255 caractères.',
            'visio_url.url' => 'Le lien visio doit être une URL valide.',
            'visio_url.max' => 'Le lien visio est trop long.',
            'agenda_items.required' => 'L\'ordre du jour doit contenir au moins un point.',
            'agenda_items.min' => 'L\'ordre du jour doit contenir au moins un point.',
            'agenda_items.*.title.required' => 'Le titre de chaque point est obligatoire.',
            'agenda_items.*.title.max' => 'Le titre d\'un point ne peut pas dépasser 255 caractères.',
            'agenda_items.*.duration_minutes.integer' => 'La durée d\'un point doit être un nombre entier.',
            'agenda_items.*.duration_minutes.min' => 'La durée d\'un point doit être d\'au moins 1 minute.',
        ];
    }
}
