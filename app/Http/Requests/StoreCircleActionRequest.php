<?php

namespace App\Http\Requests;

use App\Models\CircleAction;
use Illuminate\Foundation\Http\FormRequest;

class StoreCircleActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [CircleAction::class, $this->route('circle')]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'due_date' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'title.max' => 'Le titre ne peut pas dépasser 150 caractères.',
            'due_date.required' => 'La date d\'échéance est obligatoire.',
            'due_date.date' => 'La date d\'échéance est invalide.',
        ];
    }
}
