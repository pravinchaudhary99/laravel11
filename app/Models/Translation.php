<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Translation extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'source' => 'boolean',
    ];

    protected $with = [
        'language',
    ];

    public function phrases(): HasMany
    {
        return $this->hasMany(Phrase::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function scopeIsSource($query): void
    {
        $query->where('source', true);
    }

    public function scopeWithProgress($query): void
    {
        $query->addSelect([
            'progress' => Phrase::selectRaw('COUNT(CASE WHEN value IS NOT NULL THEN 1 END) / COUNT(*) * 100')
                ->whereColumn('phrases.translation_id', 'translations.id')
                ->limit(1),
        ])->withCasts([
            'progress' => 'decimal:1',
        ]);
    }
}
