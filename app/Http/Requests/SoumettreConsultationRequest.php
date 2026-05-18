<?php

namespace App\Http\Requests;

use App\Enums\ConsultationMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SoumettreConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $consultation = $this->route('consultation');

        return match ($consultation->mode_recueil) {
            ConsultationMode::AvisLibre => [
                'contenu' => ['required', 'string', 'max:500'],
            ],
            ConsultationMode::Signature => [
                'prenom' => ['required', 'string', 'max:100'],
                'nom' => ['required', 'string', 'max:100'],
            ],
            ConsultationMode::VoteIndicatif => [
                'choix' => ['required', 'string', Rule::in($consultation->options ?? [])],
            ],
        };
    }

    public function messages(): array
    {
        return [
            'contenu.required' => 'Votre avis ne peut pas être vide.',
            'contenu.max' => 'Votre avis ne peut pas dépasser 500 caractères.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'nom.required' => 'Le nom est obligatoire.',
            'choix.required' => 'Veuillez sélectionner une option.',
            'choix.in' => "L'option choisie n'est pas valide.",
        ];
    }
}
