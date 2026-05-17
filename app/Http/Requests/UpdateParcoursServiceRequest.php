<?php

namespace App\Http\Requests;

use App\Enums\ParcoursCtaType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateParcoursServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('service'));
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'use_cases' => ['required', 'string'],
            'cta_type' => ['required', Rule::enum(ParcoursCtaType::class)],
            'cta_value' => ['required', 'string', 'max:512'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
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
