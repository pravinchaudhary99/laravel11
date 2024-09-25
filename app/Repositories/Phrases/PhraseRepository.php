<?php

namespace App\Repositories\Phrases;

use App\Models\Phrase;
use App\Models\Translation;
use Illuminate\Http\Request;
use App\Models\TranslationFile;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\Phrases\PhraseInterface;

class PhraseRepository implements PhraseInterface
{
    protected $responsesData = array();

    protected $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function index($id) {
        $translation = Translation::find($id);

        $phrases = $translation->phrases()->newQuery();

        $files = [];
        foreach (collect($phrases->where('translation_id', $translation->id)->get())->unique('translation_file_id') as $value) {
            $files[] = TranslationFile::where('id', $value->translation_file_id)->first();
        }

        $this->responsesData['success'] = true;
        $this->responsesData['data'] = [
            'translation' => $translation,
            'files' => $files
        ];
        return $this->responsesData;
    }

    public function list($id) {
        $direction = $this->request->order[0]['dir'];
        $skip = $this->request->start;
        $take = $this->request->length;
        $searchValue = $this->request->search['value'];
        $translationFile = $this->request->translationFile ?? null;
        $status = $this->request->status ?? null;

        $translation = Translation::find($id);
        $sourceTranslation = Translation::where('source', true)->first();

        $phrases = $translation->phrases()->newQuery();

        $recordsTotal = $phrases->count();

        if ($searchValue) {
            $phrases->whereAny(['key', 'value'], 'like', '%' . $searchValue . '%');
        }

        if ($translationFile) {
            $phrases->where(
                $translationFile
                    ? fn (Builder $query) => $query->where('translation_file_id', $translationFile)
                    : fn (Builder $query) => $query->whereNull('translation_file_id')
            );
        }

        if ($status) {
            $phrases->where(
                $status === 'translated'
                    ? fn (Builder $query) => $query->whereNotNull('value')
                    : fn (Builder $query) => $query->whereNull('value')
            );
        }

        $recordsFiltered = $phrases->count();

        $phrases = $phrases->orderBy('key', $direction)->skip($skip)->take($take)->get()
                ->map(function($query) use($translation, $sourceTranslation) {
                    $enValue = $sourceTranslation->phrases()->where('key', $query->key)->value('value');
                    $query->enValue = $enValue;
                    return $query;
                });
        
        $this->responsesData['data'] = [
            'data' => isset($phrases) ? $phrases : [],
            'recordsTotal' => isset($recordsTotal) ? $recordsTotal : 0,
            'recordsFiltered' => isset($recordsFiltered) ? $recordsFiltered : 0,
        ];
        return $this->responsesData;
    }

    public function update($id) {
        $phrase = Phrase::where( 'uuid', $id)->first();
        $value = $this->request->value;

        $phrase->update(['value' => $value]);
        $this->responsesData['success'] = true;
        return $this->responsesData;
    }

    public function translate($id) {
        $sourceTranslation = Translation::where('source', true)->first();
        $phrase = Phrase::where( 'uuid', $id)->first();
        $key = $this->request->key;

        if(!$phrase){
            $this->responsesData['success'] = false;
            return $this->responsesData;
        }

        $translatedValue = translate($sourceTranslation->language->code, $phrase->translation->language->code, $key);

        $this->responsesData['success'] = true;
        $this->responsesData['data'] = ["translated" => $translatedValue];
        return $this->responsesData;
    }
}