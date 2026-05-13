<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateLabServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('service'));
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'title.max' => 'Le titre ne peut pas dépasser 150 caractères.',
            'category.required' => 'La catégorie est obligatoire.',
            'category.max' => 'La catégorie ne peut pas dépasser 100 caractères.',
            'description.required' => 'La description est obligatoire.',
        ];
    }
}
