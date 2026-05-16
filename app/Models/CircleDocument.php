<?php

namespace App\Models;

use App\Enums\CircleDocumentType;
use Database\Factories\CircleDocumentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CircleDocument extends Model
{
    /** @use HasFactory<CircleDocumentFactory> */
    use HasFactory;

    protected $fillable = [
        'circle_id',
        'uploaded_by',
        'title',
        'type',
        'document_date',
        'tags',
        'description',
        'url',
        'file_path',
        'original_filename',
    ];

    protected function casts(): array
    {
        return [
            'document_date' => 'date',
            'tags' => 'array',
            'type' => CircleDocumentType::class,
        ];
    }

    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function isPdf(): bool
    {
        return $this->type === CircleDocumentType::Pdf;
    }

    public function isLink(): bool
    {
        return $this->type === CircleDocumentType::Link;
    }

    public function getStorageUrl(): string
    {
        return Storage::url($this->file_path);
    }
}
