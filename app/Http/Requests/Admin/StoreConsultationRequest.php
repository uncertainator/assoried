<?php

namespace App\Http\Requests\Admin;

use App\Enums\ConsultationMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $isVote = $this->input('mode_recueil') === ConsultationMode::VoteIndicatif->value;

        return [
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date_cloture' => ['nullable', 'date', 'after:now'],
            'mode_recueil' => ['required', Rule::enum(ConsultationMode::class)],
            'options' => $isVote ? ['required', 'array', 'min:2'] : ['nullable', 'array'],
            'options.*' => $isVote ? ['required', 'string', 'max:200'] : ['nullable', 'string', 'max:200'],
            'masque' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'titre.required' => 'Le titre est obligatoire.',
            'titre.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'date_cloture.after' => 'La date de clôture doit être dans le futur.',
            'mode_recueil.required' => 'Le mode de recueil est obligatoire.',
            'options.required' => 'Les options de vote sont obligatoires pour un vote indicatif.',
            'options.min' => 'Au moins 2 options sont requises.',
            'options.*.required' => 'Chaque option ne peut pas être vide.',
            'options.*.max' => 'Chaque option ne peut pas dépasser 200 caractères.',
        ];
    }
}
