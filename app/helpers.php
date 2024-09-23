<?php

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
