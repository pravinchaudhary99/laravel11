<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Language extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function translation(): HasOne
    {
        return $this->hasOne(Translation::class);
    }

    public function phrases(): HasManyThrough
    {
        return $this->hasManyThrough(Phrase::class, Translation::class);
    }
}
