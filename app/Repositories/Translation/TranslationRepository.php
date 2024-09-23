<?php
namespace App\Repositories\Translation;

use App\Models\Language;
use App\Models\Translation;
use Illuminate\Http\Request;
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
                            ->where('source', true)
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
}