<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [Post::class, $this->route('circle')]);
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:5000'],
            'push_to_general' => ['boolean'],
        ];
    }
}
