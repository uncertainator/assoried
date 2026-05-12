<?php

namespace App\Http\Requests;

use App\Enums\CircleActionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateCircleActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('action'));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(CircleActionStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Le statut est obligatoire.',
        ];
    }
}
