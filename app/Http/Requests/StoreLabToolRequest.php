<?php

namespace App\Http\Requests;

use App\Models\LabTool;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreLabToolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', LabTool::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'active' => ['boolean'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:20480'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'title.max' => 'Le titre ne peut pas dépasser 150 caractères.',
            'category.max' => 'La catégorie ne peut pas dépasser 100 caractères.',
            'file.required' => 'Le fichier PDF est obligatoire.',
            'file.mimes' => 'Le fichier doit être un PDF.',
            'file.max' => 'Le fichier ne peut pas dépasser 20 Mo.',
        ];
    }
}
