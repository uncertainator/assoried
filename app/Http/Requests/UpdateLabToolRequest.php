<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateLabToolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('tool'));
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'active' => ['boolean'],
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'title.max' => 'Le titre ne peut pas dépasser 150 caractères.',
            'category.max' => 'La catégorie ne peut pas dépasser 100 caractères.',
            'file.mimes' => 'Le fichier doit être un PDF.',
            'file.max' => 'Le fichier ne peut pas dépasser 20 Mo.',
        ];
    }
}
