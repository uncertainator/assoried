<?php

namespace App\Http\Requests\Admin;

use App\Enums\ScrutinMajorityType;
use App\Enums\ScrutinQuorumType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateScrutinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('scrutin'));
    }

    public function rules(): array
    {
        $isQualified = $this->input('majority_type') === ScrutinMajorityType::Qualified->value;

        return [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'opened_at' => ['required', 'date'],
            'closes_at' => ['required', 'date', 'after:opened_at'],
            'quorum_type' => ['required', Rule::enum(ScrutinQuorumType::class)],
            'quorum_value' => ['required', 'numeric', 'min:0'],
            'majority_type' => ['required', Rule::enum(ScrutinMajorityType::class)],
            'majority_threshold' => $isQualified
                ? ['required', 'numeric', 'min:0', 'max:100']
                : ['nullable', 'numeric'],
            'options' => ['required', 'array', 'min:2'],
            'options.*.label' => ['required', 'string', 'max:200'],
            'options.*.position' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'opened_at.required' => "La date d'ouverture est obligatoire.",
            'closes_at.required' => 'La date de clôture est obligatoire.',
            'closes_at.after' => "La date de clôture doit être postérieure à la date d'ouverture.",
            'quorum_value.required' => 'La valeur du quorum est obligatoire.',
            'majority_threshold.required' => 'Le seuil de majorité qualifiée est obligatoire.',
            'majority_threshold.max' => 'Le seuil ne peut pas dépasser 100 %.',
            'options.required' => 'Au moins 2 options sont requises.',
            'options.min' => 'Au moins 2 options sont requises.',
            'options.*.label.required' => 'Chaque option doit avoir un libellé.',
        ];
    }
}
