<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VoteScrutinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('vote', $this->route('scrutin'));
    }

    public function rules(): array
    {
        $validOptionIds = $this->route('scrutin')->options()->pluck('id')->toArray();

        return [
            'scrutin_option_id' => ['required', 'integer', Rule::in($validOptionIds)],
        ];
    }

    public function messages(): array
    {
        return [
            'scrutin_option_id.required' => 'Veuillez sélectionner une option.',
            'scrutin_option_id.in' => "L'option sélectionnée est invalide.",
        ];
    }
}
