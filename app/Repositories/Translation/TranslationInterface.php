<?php
namespace App\Repositories\Translation;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface TranslationInterface
{
    public function list();
}