<?php

namespace App\Models;

use Database\Factories\ScrutinVoteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScrutinVote extends Model
{
    /** @use HasFactory<ScrutinVoteFactory> */
    use HasFactory;

    protected $fillable = ['scrutin_id', 'scrutin_option_id', 'user_id'];

    public function scrutin(): BelongsTo
    {
        return $this->belongsTo(Scrutin::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(ScrutinOption::class, 'scrutin_option_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
