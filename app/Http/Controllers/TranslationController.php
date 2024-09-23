<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Repositories\Translation\TranslationInterface;

class TranslationController extends Controller
{
    protected $repo;

    public function __construct(TranslationInterface $interface) {
        $this->repo = $interface;
    }


    public function index() {
        $languages = Language::query()
                        ->leftJoin('translations', 'translations.language_id', '=', 'languages.id')
                        ->whereNull('translations.id')
                        ->get(['languages.*']);

        return view('translations.index', compact('languages'));
    }

    public function list() {
        try {
           $responses = $this->repo->list();

           $data = $responses['data'];
           return response()->json($data);
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }
}
