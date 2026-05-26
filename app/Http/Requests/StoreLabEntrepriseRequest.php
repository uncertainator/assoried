<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLabEntrepriseRequest extends FormRequest
{
    protected $errorBag = 'entreprise';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom_contact' => ['required', 'string', 'max:150'],
            'fonction' => ['required', 'string', 'max:150'],
            'raison_sociale' => ['required', 'string', 'max:200'],
            'email' => ['required', 'email', 'max:255'],
            'telephone' => ['required', 'string', 'max:30'],
            'taille_organisation' => ['nullable', 'string', 'in:1–10,11–50,51–200,200+'],
            'thematique' => ['required', 'string', 'in:Design Thinking,Intelligence Collective,Stratégie,Gestion de projet,Entrepreneuriat,Autre'],
            'message' => ['required', 'string', 'max:1200'],
            'rgpd_consent' => ['required', 'accepted'],
            '_pot' => ['nullable', 'max:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom_contact.required' => 'Votre prénom et nom sont requis.',
            'nom_contact.max' => 'Le nom ne peut pas dépasser 150 caractères.',
            'fonction.required' => 'Votre fonction est requise.',
            'fonction.max' => 'La fonction ne peut pas dépasser 150 caractères.',
            'raison_sociale.required' => 'Le nom de l\'entreprise ou de la structure est requis.',
            'raison_sociale.max' => 'Le nom de l\'entreprise ne peut pas dépasser 200 caractères.',
            'email.required' => 'L\'adresse email professionnelle est requise.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'telephone.required' => 'Un numéro de téléphone direct est requis.',
            'telephone.max' => 'Le numéro de téléphone ne peut pas dépasser 30 caractères.',
            'taille_organisation.in' => 'La taille d\'organisation sélectionnée n\'est pas valide.',
            'thematique.required' => 'Veuillez sélectionner une thématique.',
            'thematique.in' => 'La thématique sélectionnée n\'est pas valide.',
            'message.required' => 'Veuillez décrire votre besoin.',
            'message.max' => 'La description ne peut pas dépasser 1 200 caractères.',
            'rgpd_consent.required' => 'Vous devez accepter la politique de confidentialité pour soumettre ce formulaire.',
            'rgpd_consent.accepted' => 'Vous devez accepter la politique de confidentialité pour soumettre ce formulaire.',
        ];
    }
}
