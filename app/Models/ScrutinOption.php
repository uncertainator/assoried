<?php

namespace App\Models;

use Database\Factories\ScrutinOptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScrutinOption extends Model
{
    /** @use HasFactory<ScrutinOptionFactory> */
    use HasFactory;

    protected $fillable = ['scrutin_id', 'label', 'position'];

    public function scrutin(): BelongsTo
    {
        return $this->belongsTo(Scrutin::class);
    }
}
