<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateParcoursQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('question'));
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:255'],
            'sort_order' => ['integer', 'min:0'],
            'options' => ['nullable', 'array'],
            'options.*.id' => ['nullable', 'integer', 'exists:parcours_options,id'],
            'options.*.label' => ['required', 'string', 'max:255'],
            'options.*.sort_order' => ['integer', 'min:0'],
            'options.*.next_question_id' => ['nullable', 'integer', 'exists:parcours_questions,id'],
            'options.*.service_id' => ['nullable', 'integer', 'exists:parcours_services,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'label.required' => 'Le libellé de la question est obligatoire.',
            'options.*.label.required' => 'Le libellé de chaque option est obligatoire.',
        ];
    }
}
