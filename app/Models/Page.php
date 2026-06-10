<?php

namespace App\Models;

use App\Support\HtmlSanitizer;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'title', 'content', 'updated_by'];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Sanitize rich-text HTML on every write so nothing unsafe is ever persisted.
     */
    protected function content(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value): string => HtmlSanitizer::clean($value),
        );
    }
}
