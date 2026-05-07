<?php

namespace App\Http\Requests\Admin;

use App\Models\Circle;
use Illuminate\Foundation\Http\FormRequest;

class UserPromoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'circle_id' => ['required', 'integer', 'exists:circles,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->route('user');

            if ($user->isAdmin()) {
                $validator->errors()->add('circle_id', 'Un administrateur ne peut pas être promu référent.');

                return;
            }

            if ($user->isReferent()) {
                $validator->errors()->add('circle_id', 'Cet utilisateur est déjà référent d\'un cercle.');

                return;
            }

            if ($this->filled('circle_id')) {
                $circle = Circle::find($this->circle_id);
                if ($circle && $circle->referent_id !== null) {
                    $validator->errors()->add('circle_id', 'Ce cercle a déjà un référent.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'circle_id.required' => 'Veuillez sélectionner un cercle.',
            'circle_id.exists' => 'Le cercle sélectionné n\'existe pas.',
        ];
    }
}
