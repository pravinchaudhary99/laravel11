<?php

namespace App\Repositories\Translation;

interface TranslationInterface
{
    public function list();

    public function store();

    public function destroy($id);
}