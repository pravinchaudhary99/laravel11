<?php

namespace App\Http\Controllers;

use App\Models\Phrase;
use App\Models\Translation;
use Illuminate\Http\Request;
use App\Models\TranslationFile;
use App\Repositories\Phrases\PhraseInterface;

class PhraseController extends Controller
{
    protected $repo;

    public function __construct(PhraseInterface $interface) {
        $this->repo = $interface;
    }

    public function index($id) {
        $responses = $this->repo->index($id);
        
        $data = $responses['data'];
        $translation = $data['translation'];

        $files = $data['files'];
        return view('translations.create', compact('translation', 'files'));
    }

    public function list($id) {
        try {
            $responses = $this->repo->list($id);

            $data = $responses['data'];
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['errors', $e->getMessage()], 500);
        }
    }

    public function update($id) {
        try {
            $responses = $this->repo->update($id);

            if(!$responses['success']) {
                return response()->json(['errors', 'Phrases data not fount'], 500);
            }
            return response()->json(['message' => 'Phrases has been updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['errors', $e->getMessage()], 500);
        }
    }

    public function translate($id) {
        try {
            $responses = $this->repo->translate($id);

            if(!$responses['success']) {
                return response()->json(['errors', 'Phrases data not fount'], 500);
            }

            $data = $responses['data'];
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['errors', $e->getMessage()], 500);
        }
    }
}
