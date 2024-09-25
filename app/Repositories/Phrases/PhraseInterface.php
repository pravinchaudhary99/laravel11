<?php

namespace App\Repositories\Phrases;

interface PhraseInterface
{
    public function index($id);

    public function list($id);

    public function update($id);

    public function translate($id);
}

