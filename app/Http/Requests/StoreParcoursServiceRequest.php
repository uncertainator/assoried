<?php

namespace App\Http\Requests;

use App\Enums\ParcoursCtaType;
use App\Models\ParcoursService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreParcoursServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', ParcoursService::class);
    }

    public function rules(): array
    {
        return [
            'slug'             => ['nullable', 'string', 'max:100', 'unique:parcours_services,slug'],
            'name'             => ['required', 'string', 'max:150'],
            'description'      => ['required', 'string'],
            'use_cases'        => ['nullable', 'string'],
            'pour_qui'         => ['nullable', 'string'],
            'ce_que_ca_produit'=> ['nullable', 'string'],
            'format'           => ['nullable', 'string', 'max:200'],
            'branche'          => ['nullable', 'string', 'max:80'],
            'cta_type'         => ['required', Rule::enum(ParcoursCtaType::class)],
            'cta_value'        => ['required', 'string', 'max:512'],
            'is_active'        => ['boolean'],
            'sort_order'       => ['integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du service est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'use_cases.required' => 'Les cas d\'usage sont obligatoires.',
            'cta_type.required' => 'Le type d\'action est obligatoire.',
            'cta_value.required' => 'L\'URL ou email de l\'action est obligatoire.',
        ];
    }
}
