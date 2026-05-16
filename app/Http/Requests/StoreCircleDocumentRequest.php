<?php

namespace App\Http\Requests;

use App\Models\CircleDocument;
use Illuminate\Foundation\Http\FormRequest;

class StoreCircleDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [CircleDocument::class, $this->route('circle')]);
    }

    protected function prepareForValidation(): void
    {
        $raw = $this->input('tags_input', '');
        $tags = collect(explode(',', $raw))
            ->map(fn (string $t) => trim($t))
            ->filter()
            ->values()
            ->all();

        $this->merge(['tags' => $tags ?: null]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:pdf,link'],
            'document_date' => ['required', 'date', 'before_or_equal:today'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'description' => ['nullable', 'string', 'max:5000'],
            'file' => ['required_if:type,pdf', 'nullable', 'file', 'mimes:pdf', 'max:10240'],
            'url' => ['required_if:type,link', 'nullable', 'url', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'type.required' => 'Le type de document est obligatoire.',
            'type.in' => 'Le type doit être "pdf" ou "lien".',
            'document_date.required' => 'La date du document est obligatoire.',
            'document_date.date' => 'La date du document n\'est pas valide.',
            'document_date.before_or_equal' => 'La date ne peut pas être dans le futur.',
            'tags.array' => 'Les tags doivent être une liste.',
            'file.required_if' => 'Le fichier PDF est obligatoire pour ce type.',
            'file.mimes' => 'Seuls les fichiers PDF sont acceptés.',
            'file.max' => 'Le fichier ne doit pas dépasser 10 Mo.',
            'url.required_if' => 'L\'URL est obligatoire pour un lien externe.',
            'url.url' => 'L\'URL n\'est pas valide (doit commencer par http:// ou https://).',
            'url.max' => 'L\'URL est trop longue.',
        ];
    }
}
