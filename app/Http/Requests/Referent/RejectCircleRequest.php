<?php

namespace App\Http\Requests\Referent;

use Illuminate\Foundation\Http\FormRequest;

class RejectCircleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
