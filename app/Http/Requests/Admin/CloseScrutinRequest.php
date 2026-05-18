<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CloseScrutinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('close', $this->route('scrutin'));
    }

    public function rules(): array
    {
        return [];
    }
}
