<?php

namespace App\Http\Requests;

use App\Models\LabService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreLabServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', LabService::class);
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
