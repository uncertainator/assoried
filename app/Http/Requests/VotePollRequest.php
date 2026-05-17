<?php

namespace App\Http\Requests;

use App\Enums\PollType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VotePollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('vote', $this->route('poll'));
    }

    public function rules(): array
    {
        $poll = $this->route('poll');

        $allowed = $poll->type === PollType::YesNo
            ? ['oui', 'non']
            : ($poll->options ?? []);

        return [
            'choice' => ['required', 'string', Rule::in($allowed)],
        ];
    }

    public function messages(): array
    {
        return [
            'choice.required' => 'Veuillez sélectionner une option.',
            'choice.in' => 'Le choix sélectionné est invalide.',
        ];
    }
}
