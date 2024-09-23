<?php

namespace App\Models;

use App\Models\TranslationFile;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Phrase extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [];

    protected $casts = [
        'parameters' => 'array',
    ];

    protected $with = [
        'source', 'file',
    ];

    public function file(): BelongsTo
    {
        return $this->belongsTo(TranslationFile::class, 'translation_file_id');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Phrase::class, 'phrase_id');
    }

    public function translation(): BelongsTo
    {
        return $this->belongsTo(Translation::class);
    }

    public function similarPhrases(): Collection
    {
        return $this->translation->phrases()->where('key', 'like', "%$this->key%")
            ->whereKeyNot($this->id)
            ->get();
    }
}
