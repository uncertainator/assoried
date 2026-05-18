<?php

namespace App\Http\Requests\Admin;

use App\Enums\ConsultationMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTerrainReponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $consultation = $this->route('consultation');

        return match ($consultation->mode_recueil) {
            ConsultationMode::AvisLibre => [
                'reponses' => ['required', 'array', 'min:1'],
                'reponses.*.contenu' => ['required', 'string', 'max:500'],
            ],
            ConsultationMode::Signature => [
                'reponses' => ['required', 'array', 'min:1'],
                'reponses.*.prenom' => ['required', 'string', 'max:100'],
                'reponses.*.nom' => ['required', 'string', 'max:100'],
            ],
            ConsultationMode::VoteIndicatif => [
                'reponses' => ['required', 'array', 'min:1'],
                'reponses.*.choix' => ['required', 'string', Rule::in($consultation->options ?? [])],
            ],
        };
    }

    public function messages(): array
    {
        return [
            'reponses.required' => 'Au moins une réponse est requise.',
            'reponses.min' => 'Au moins une réponse est requise.',
            'reponses.*.contenu.required' => 'Le contenu de chaque réponse est obligatoire.',
            'reponses.*.contenu.max' => 'Chaque avis ne peut pas dépasser 500 caractères.',
            'reponses.*.prenom.required' => 'Le prénom est obligatoire.',
            'reponses.*.nom.required' => 'Le nom est obligatoire.',
            'reponses.*.choix.required' => 'Veuillez sélectionner une option.',
            'reponses.*.choix.in' => "L'option choisie n'est pas valide.",
        ];
    }
}
