<?php

namespace Snayvik\Translation\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Snayvik\Translation\Models\SnvkTranslation;
use Snayvik\Translation\Services\TranslationService;

class ImportTranslationInFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translation:import:files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Write database tranlastion into lang directory files';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public $lang_dir;

    public $json_group = '_json';

    public function __construct()
    {
        parent::__construct();
        $this->lang_dir = App::langPath();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $map = TranslationService::getTranslationMap(SnvkTranslation::all());
        $locales = SnvkTranslation::groupBy('locale')->pluck('locale')->toArray();
        foreach($locales as $locale){
            if(isset($map[$locale])){               
                foreach($map[$locale] as $group => $translations){                   
                    if($group != $this->json_group){
                        $locale_dir = $this->lang_dir.'/'.$locale;
                        if(!is_dir($locale_dir)){
                            mkdir($locale_dir);
                        }

                        if(is_dir($locale_dir)){

                            $group_file = $locale_dir.'/'.$group.'.php';                            
                            if(!is_file($group_file)){
                                File::put($group_file, 'txt');
                            }

                            $content = '<?php '."\r\n \r\n".' return array ( '."\r\n";

                            foreach($translations as $key => $value){
                                $content .= ' "'.$key.'" => "'.$value->value.'",'."\r\n";
                            }

                            $content .= ');';

                            File::put($group_file, $content);
                        }
                    }else{
                        // Log::info(json_encode($translations));
                        $json_file = $this->lang_dir.'/'.$locale.'.json';
                        $content = [];
                        foreach($translations as $key => $value){
                            $content[$key] = $value->value;
                        }

                        // if(is_file($json_file)){
                            File::put($json_file, json_encode($content));
                        // }
                    }
                }
            }
        }

        return 0;
    }
}
