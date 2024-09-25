<?php

use Stichoza\GoogleTranslate\GoogleTranslate;

if (! function_exists('getPhraseParameters')) {
    function getPhraseParameters(string $phrase): ?array
    {
        preg_match_all('/(?<!\w):(\w+)/', $phrase, $matches);

        if (empty($matches[1])) {
            return null;
        }

        return $matches[1];
    }
}

if(! function_exists('translate')) {
    function translate($sourceLang, $targetLang, $text) {
        return (new GoogleTranslate())
            ->preserveParameters()
            ->setSource($sourceLang)
            ->setTarget($targetLang)
            ->translate($text);
    }
}
