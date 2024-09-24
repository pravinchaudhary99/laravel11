<?php
namespace App\Repositories\Translation;

use App\Models\Phrase;
use App\Models\Language;
use App\Models\Translation;
use Illuminate\Http\Request;
use App\Models\TranslationFile;
use Illuminate\Support\Facades\Log;
use App\Repositories\Translation\TranslationInterface;

class TranslationRepository implements TranslationInterface
{
    protected $responsesData = array();

    protected $request;

    public function __construct(Request $request){
        $this->request = $request;
    }

    public function list() {
        $orderBy = $this->request->order[0]['column'];
        $direction = $this->request->order[0]['dir'];
        $skip = $this->request->start;
        $take = $this->request->length;
        $searchValue = $this->request->search['value'];
        $columns = ['id', 'created_at'];

        $translations = Translation::query()
                            ->with('language')
                            ->withCount('phrases')
                            ->withProgress()
                            // ->where('source', true)
                            ->orderBy($columns[$orderBy], $direction);

        $recordsTotal = $translations->count();

        if ($searchValue) {
            $translations->whereHas('language', function ($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%');
            });
        }

        $recordsFiltered = $translations->count();

        $translations = $translations->skip($skip)->take($take)->get()->map(function ($query) {
            $query->language_name = $query->language->name ?? null;
            return $query;
        });
        
        $this->responsesData['data'] = [
            'data' => isset($translations) ? $translations : [],
            'recordsTotal' => isset($recordsTotal) ? $recordsTotal : 0,
            'recordsFiltered' => isset($recordsFiltered) ? $recordsFiltered : 0,
        ];
        return $this->responsesData;
    }

    public function store() {
        $language = $this->request->language ?? null;

        $langId = Language::find( $language);    
        if(!$langId) {
            $this->responsesData['success'] = false;
            return $this->responsesData; 
        }

        $translation = Translation::updateOrCreate(['language_id' => $language], ['source' => false]);
        $this->connectPhases($translation);

        $this->responsesData['success'] = true;
        return $this->responsesData;
    }

    private function connectPhases($translation) {
        $sourceTranslation = Translation::where('source', true)->first();


        $sourceTranslation->phrases()->with('file')->get()->each(function ($sourcePhrase) use ($translation) {
            $file = $sourcePhrase->file;

            if ($file && $file->is_root) {
                $file = TranslationFile::firstOrCreate([
                    'is_root' => true,
                    'extension' => $file->extension,
                    'name' => $translation->language->code,
                ]);
            }

            // Now create the phrase under translation
            $translation->phrases()->create([
                'value' => null,
                'key' => $sourcePhrase->key,
                'group' => $file->name,
                'parameters' => $sourcePhrase->parameters,
                'translation_file_id' => $file->id,
            ]);
        });
    }

    public function destroy($id)
    {
        $translation = Translation::where('source', false)->find($id);
        
        if(!$translation){
            $this->responsesData['success'] = false;
            return $this->responsesData;
        }

        Phrase::where('translation_id', $id)->delete();
        $translation->delete();
        
        $this->responsesData['success'] = true;
        return $this->responsesData;
    }
}