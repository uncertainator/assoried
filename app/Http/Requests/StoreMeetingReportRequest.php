<?php

namespace App\Http\Requests;

use App\Models\Meeting;
use App\Models\MeetingReport;
use Illuminate\Foundation\Http\FormRequest;

class StoreMeetingReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Meeting $meeting */
        $meeting = $this->route('meeting');

        return $this->user()->can('create', [MeetingReport::class, $meeting]);
    }

    public function rules(): array
    {
        return [
            'participants' => ['nullable', 'string', 'max:5000'],
            'agenda_notes' => ['nullable', 'array'],
            'agenda_notes.*' => ['nullable', 'string', 'max:2000'],
            'decisions' => ['nullable', 'array'],
            'decisions.*' => ['nullable', 'string', 'max:500'],
            'open_points' => ['nullable', 'array'],
            'open_points.*' => ['nullable', 'string', 'max:500'],
            'free_notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'participants.max' => 'Le champ participants ne doit pas dépasser 5000 caractères.',
            'agenda_notes.array' => 'Le suivi de l\'ordre du jour doit être un tableau.',
            'agenda_notes.*.max' => 'Chaque note de suivi ne doit pas dépasser 2000 caractères.',
            'decisions.array' => 'Les décisions doivent être un tableau.',
            'decisions.*.max' => 'Chaque décision ne doit pas dépasser 500 caractères.',
            'open_points.array' => 'Les points ouverts doivent être un tableau.',
            'open_points.*.max' => 'Chaque point ouvert ne doit pas dépasser 500 caractères.',
            'free_notes.max' => 'Les notes libres ne doivent pas dépasser 5000 caractères.',
        ];
    }
}
