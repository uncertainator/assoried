<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLabCitoyenRequest extends FormRequest
{
    protected $errorBag = 'citoyen';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom_contact' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:30'],
            'type_projet' => ['required', 'string', 'in:Initiative citoyenne,Projet associatif,Projet personnel,Autre'],
            'message' => ['required', 'string', 'max:800'],
            'rgpd_consent' => ['required', 'accepted'],
            '_pot' => ['nullable', 'max:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom_contact.required' => 'Votre prénom et nom sont requis.',
            'nom_contact.max' => 'Le nom ne peut pas dépasser 150 caractères.',
            'email.required' => 'Votre adresse email est requise.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'telephone.max' => 'Le numéro de téléphone ne peut pas dépasser 30 caractères.',
            'type_projet.required' => 'Veuillez sélectionner un type de projet.',
            'type_projet.in' => 'Le type de projet sélectionné n\'est pas valide.',
            'message.required' => 'Veuillez décrire votre projet.',
            'message.max' => 'La description ne peut pas dépasser 800 caractères.',
            'rgpd_consent.required' => 'Vous devez accepter la politique de confidentialité pour soumettre ce formulaire.',
            'rgpd_consent.accepted' => 'Vous devez accepter la politique de confidentialité pour soumettre ce formulaire.',
        ];
    }
}
