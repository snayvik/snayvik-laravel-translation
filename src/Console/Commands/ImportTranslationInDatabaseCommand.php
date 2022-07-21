<?php

namespace Snayvik\Translation\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Snayvik\Translation\Services\TranslationService;

class ImportTranslationInDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translation:import:db {replace} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import lang directory translation into database';

    public $lang_dir;

    public $json_group = '_json';
    public $translationService;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TranslationService $translationService)
    {
        parent::__construct();
        $this->lang_dir = App::langPath();
        $this->translationService = $translationService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->jsonFilesImport();
        $this->directoryFilesImport();

        return 0;
    }

    public function jsonFilesImport(){
        $lang_files = File::files($this->lang_dir); // Get all files in lang directory
        foreach($lang_files as $file){            
            if($file->getExtension() == 'json'){ // Filter json files
                $locale = File::name($file);
                $json = File::get($file->getRealPath());
                $array = json_decode($json);
                foreach($array as $key => $value){
                    $this->translationService->SaveToDb($locale, $this->json_group, $key, $value, $this->argument('replace'));                    
                }
            }
        }

        return true;
    }

    public function directoryFilesImport(){
        $lang_directories = File::directories($this->lang_dir);
        $locales = [];
        $files = [];
        foreach ($lang_directories as $dir){
            $dir_name = File::name($dir);
            $files[$dir_name] = File::files($dir);

            $locales[] = $dir_name;
        }

        
        foreach($locales as $locale){
            if(isset($files[$locale])){

                foreach($files[$locale] as $file){
                    $group = File::name($file);
                    $array_data = include $file;
                    if(gettype($array_data) == 'array'){
                        foreach($array_data as $key => $value){
                            $this->translationService->SaveToDb($locale, $group, $key, $value, $this->argument('replace'));
                        }
                    }
                }
            }
        }

        return true;
    }
}
