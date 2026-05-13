<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLabEntrepriseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'raison_sociale' => ['required', 'string', 'max:200'],
            'nom_contact' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255'],
            'telephone' => ['required', 'string', 'max:30'],
            'besoin_type' => ['nullable', 'string', 'in:facilitation,innovation,autre'],
            'message' => ['required', 'string', 'max:3000'],
            'rgpd_consent' => ['required', 'accepted'],
            '_pot' => ['nullable', 'max:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'raison_sociale.required' => 'La raison sociale de votre entreprise est requise.',
            'raison_sociale.max' => 'La raison sociale ne peut pas dépasser 200 caractères.',
            'nom_contact.required' => 'Le nom du contact est requis.',
            'nom_contact.max' => 'Le nom du contact ne peut pas dépasser 150 caractères.',
            'email.required' => 'L\'adresse email est requise.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'telephone.required' => 'Le numéro de téléphone est requis.',
            'telephone.max' => 'Le numéro de téléphone ne peut pas dépasser 30 caractères.',
            'besoin_type.in' => 'Le type de besoin sélectionné n\'est pas valide.',
            'message.required' => 'Veuillez décrire votre besoin.',
            'message.max' => 'Le message ne peut pas dépasser 3 000 caractères.',
            'rgpd_consent.required' => 'Vous devez accepter la politique de confidentialité pour soumettre ce formulaire.',
            'rgpd_consent.accepted' => 'Vous devez accepter la politique de confidentialité pour soumettre ce formulaire.',
        ];
    }
}
