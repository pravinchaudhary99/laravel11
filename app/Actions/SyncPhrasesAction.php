<?php

namespace App\Actions;

use App\Models\Language;
use App\Models\Translation;
use App\Models\TranslationFile;

class SyncPhrasesAction
{
    public static function execute(Translation $source, $key, $value, $locale, $file): void
    {
        if (is_array($value) && empty($value)) {
            return;
        }

        $language = Language::where('code', $locale)->first();

        if (! $language) {
            exit;
        }

        $translation = Translation::firstOrCreate([
            'language_id' => $language->id,
            'source' => config('translations.source_language') === $locale,
        ]);

        $isRoot = $file === $locale.'.json' || $file === $locale.'.php';
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $filePath = str_replace('.'.$extension, '', str_replace($locale.DIRECTORY_SEPARATOR, '', $file));

        $translationFile = TranslationFile::firstOrCreate([
            'name' => $filePath,
            'extension' => $extension,
            'is_root' => $isRoot,
        ]);

        $key = config('translations.include_file_in_key') && ! $isRoot ? "{$translationFile->name}.{$key}" : $key;

        $translation->phrases()->updateOrCreate([
            'key' => $key,
            'group' => $translationFile->name,
            'translation_file_id' => $translationFile->id,
        ], [
            'value' => (empty($value) ? null : $value),
            'parameters' => getPhraseParameters($value),
        ]);
    }
}