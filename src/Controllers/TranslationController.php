<?php

namespace Snayvik\Translation\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Snayvik\Translation\Models\SnvkLocale;
use Snayvik\Translation\Models\SnvkTranslation;
use Snayvik\Translation\Services\TranslationService;

class TranslationController extends Controller
{
    public $lang_dir;

    public $json_group = '_json';

    public $replace_existing = false;

    public $translationService;

    public function __construct(TranslationService $translationService){
        $this->lang_dir = App::langPath();
        $this->translationService = $translationService;
    }

    public function index(Request $request){
        $response['groups'] = SnvkTranslation::groupBy('group')->pluck('group')->toArray();
        $response['locales'] = SnvkLocale::pluck('locale')->toArray();
        return view('SnayvikTranslationView::translations.index', $response);
    }
    
    public function importInDb(Request $request){
        Artisan::call('translation:import:db '.$request->get('replace'));
        $this->translationService->findTranslations();
        return redirect()->back()->with('success', 'Translations are imported in database');
    }
    
    public function showGroup($group){
        $response = $this->getGroupData($group);
        $response['groups'] = SnvkTranslation::groupBy('group')->pluck('group')->toArray();
        $response['locales'] = SnvkLocale::pluck('locale')->toArray();
        return view('SnayvikTranslationView::translations.index', $response);
    }

    protected function getGroupData($group){
        $items = SnvkTranslation::where('group', $group)->get();

        $translations = [];

        foreach($items as $item){
            if(!isset($translations[$item->locale])){
                $translations[$item->locale] = [];
            }

            $translations[$item->locale][$item->key] = $item->value;
        }
        
        $response['translations'] = $translations;
        $response['selected_group'] = $group;
        $response['keys'] = SnvkTranslation::where('group', $group)->groupBy('key')->pluck('key')->toArray();

        return $response;
    }

    public function importInFiles(Request $request){
        Artisan::call('translation:import:files');
        return redirect()->back()->with('success', 'Translations are imported in files');
    }


    public function store(Request $request){
        $locale = $request->get('locale');
        $group = $request->get('group');
        $key = $request->get('key');
        $value = $request->get('value');

        $this->translationService->SaveToDb($locale, $group, $key, $value, true);

        $groupResponse = $this->getGroupData($group);
        $groupResponse['locales'] = SnvkLocale::pluck('locale')->toArray();

        $response['html'] = view('SnayvikTranslationView::translations.group-table', $groupResponse)->render();

        return response()->json($response);
        // if($request->wantsJson()){
            
        // }

        // return redirect()->back()->with('success', 'Tranlations has been updated');
    }

    public function localeStore(Request $request){
        $validated = $request->validate([
            'locale' => 'required|min:2|unique:snvk_locales,locale'            
        ]);

        $new = new SnvkLocale;
        $new->locale = $request->get('locale');
        $new->save();

        return redirect()->back()->with('success', 'New locale has been created');
    }

    public function localeDelete(Request $request, $locale){
        SnvkLocale::where(['locale' => $locale])->delete();
        return redirect()->back()->with('success', 'Locale has been deleted');
    }

    public function deleteTranslation(Request $request, $group, $key){
        SnvkTranslation::where(['group' => $group, 'key' => $key])->delete();

        return redirect()->back()->with('success', 'translation key has been deleted');
    }
}
