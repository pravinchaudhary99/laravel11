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

    public function store() {
        try {
            $responses = $this->repo->store();

            if(!$responses['success']) {
                return response()->json(['error' => 'Language not found'], 400);
            }
            
            return response()->json(['success' => true, 'message' => 'Translation has been added successfully'], 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function destroy($id) {
        try {
            $responses = $this->repo->destroy($id);

            if(!$responses['success']) {
                return response()->json(['error' => 'Translation not found'], 500);
            }
            
            return response()->json(['success' => true, 'message' => 'Translation has been deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }
}
