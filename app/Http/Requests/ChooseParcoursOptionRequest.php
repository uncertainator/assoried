<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChooseParcoursOptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $question = $this->route('question');

        return [
            'option_id' => [
                'required',
                'integer',
                Rule::exists('parcours_options', 'id')->where('question_id', $question->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'option_id.required' => 'Veuillez sélectionner une option.',
            'option_id.exists' => 'L\'option sélectionnée est invalide.',
        ];
    }
}
