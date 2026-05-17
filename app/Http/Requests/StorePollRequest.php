<?php

namespace App\Http\Requests;

use App\Enums\PollType;
use App\Models\Poll;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePollRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->input('type') !== PollType::Multiple->value) {
            $this->merge(['options' => null]);
        } else {
            // Filtrer les options vides soumises par le DOM masqué
            $options = array_values(array_filter(
                $this->input('options', []),
                fn ($v) => $v !== null && $v !== ''
            ));
            $this->merge(['options' => $options ?: null]);
        }
    }

    public function authorize(): bool
    {
        return $this->user()->can('create', [Poll::class, $this->route('circle')]);
    }

    public function rules(): array
    {
        $isMultiple = $this->input('type') === PollType::Multiple->value;

        return [
            'title' => ['required', 'string', 'max:200'],
            'type' => ['required', Rule::enum(PollType::class)],
            'closes_at' => ['required', 'date', 'after:now'],
            'options' => $isMultiple
                ? ['required', 'array', 'min:2']
                : ['nullable', 'array'],
            'options.*' => $isMultiple
                ? ['required', 'string', 'max:200']
                : ['nullable', 'string', 'max:200'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'title.max' => 'Le titre ne peut pas dépasser 200 caractères.',
            'type.required' => 'Le type de sondage est obligatoire.',
            'closes_at.required' => 'La date de clôture est obligatoire.',
            'closes_at.after' => 'La date de clôture doit être dans le futur.',
            'options.required' => 'Les options sont obligatoires pour un sondage à choix multiple.',
            'options.min' => 'Un sondage à choix multiple doit avoir au moins 2 options.',
        ];
    }
}
