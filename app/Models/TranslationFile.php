<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TranslationFile extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'is_root' => 'boolean',
    ];

    public function phrases(): HasMany
    {
        return $this->hasMany(Phrase::class);
    }

    public function fileName(): Attribute
    {
        return Attribute::get(function () {
            return "$this->name.$this->extension";
        });
    }
}
