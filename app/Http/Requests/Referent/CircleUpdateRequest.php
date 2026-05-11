<?php

namespace App\Http\Requests\Referent;

use Illuminate\Foundation\Http\FormRequest;

class CircleUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isReferent() || $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
        ];
    }
}
