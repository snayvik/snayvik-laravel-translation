<?php 

namespace Snayvik\Translation\Services;

use Snayvik\Translation\Models\SnvkTranslation;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Str;

class TranslationService
{
    const JSON_GROUP = '_json';

    public static function getTranslationMap($items){
        $response = [];

        foreach($items as $item){
            if(!isset($response[$item->locale])){
                $response[$item->locale] = [];
            }

            if(!isset($response[$item->locale][$item->group])){
                $response[$item->locale][$item->group] = [];
            }

            $response[$item->locale][$item->group][$item->key] = $item;
        }

        return $response;
    }

    public function SaveToDb($locale, $group, $key, $value, $replace = false){
        if(gettype($value) == 'string' || $value == ''){
            $map = TranslationService::getTranslationMap(SnvkTranslation::all());
            if(!isset($map[$locale][$group][$key])){
                $item = new SnvkTranslation();
                $item->locale = $locale;
                $item->group = $group;
                $item->key = $key;
                $item->value = $value;
                $item->save();
            }else{
                $item = $map[$locale][$group][$key];

                if($replace){
                    $item->value = $value;
                    $item->save();
                }
            }
        }

        if(gettype($value) == 'array'){
            foreach($value as $key1 => $value1){
                $this->SaveToDb($locale, $group, $key.'.'.$key1, $value1);
            }
        }
    }

    public function findTranslations($path = null)
    {
        $path = $path ?: base_path();
        $groupKeys = [];
        $stringKeys = [];
        $functions = config('SnayvikTranslation.trans_functions');

        $groupPattern =                          // See https://regex101.com/r/WEJqdL/6
            "[^\w|>]" .                          // Must not have an alphanum or _ or > before real method
            '(' . implode('|', $functions) . ')' .  // Must start with one of the functions
            "\(" .                               // Match opening parenthesis
            "[\'\"]" .                           // Match " or '
            '(' .                                // Start a new group to match:
            '[\/a-zA-Z0-9_-]+' .                 // Must start with group
            "([.](?! )[^\1)]+)+" .               // Be followed by one or more items/keys
            ')' .                                // Close group
            "[\'\"]" .                           // Closing quote
            "[\),]";                             // Close parentheses or new parameter

        $stringPattern =
            "[^\w]".                                     // Must not have an alphanum before real method
            '('.implode('|', $functions).')'.             // Must start with one of the functions
            "\(\s*".                                       // Match opening parenthesis
            "(?P<quote>['\"])".                            // Match " or ' and store in {quote}
            "(?P<string>(?:\\\k{quote}|(?!\k{quote}).)*)". // Match any string that can be {quote} escaped
            "\k{quote}".                                   // Match " or ' previously matched
            "\s*[\),]";                                    // Close parentheses or new parameter

        // Find all PHP + Twig files in the app folder, except for storage
        $finder = new Finder();
        $finder->in($path)->exclude('storage')->exclude('vendor')->name('*.php')->name('*.twig')->name('*.vue')->files();

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($finder as $file) {
            // Search the current file for the pattern
            if (preg_match_all("/$groupPattern/siU", $file->getContents(), $matches)) {
                // Get all matches
                foreach ($matches[2] as $key) {
                    $groupKeys[] = $key;
                }
            }

            if (preg_match_all("/$stringPattern/siU", $file->getContents(), $matches)) {
                foreach ($matches['string'] as $key) {
                    if (preg_match("/(^[\/a-zA-Z0-9_-]+([.][^\1)\ ]+)+$)/siU", $key, $groupMatches)) {
                        // group{.group}.key format, already in $groupKeys but also matched here
                        // do nothing, it has to be treated as a group
                        continue;
                    }

                    //TODO: This can probably be done in the regex, but I couldn't do it.
                    //skip keys which contain namespacing characters, unless they also contain a
                    //space, which makes it JSON.
                    if (! (Str::contains($key, '::') && Str::contains($key, '.'))
                         || Str::contains($key, ' ')) {
                        $stringKeys[] = $key;
                    }
                }
            }
        }
        // Remove duplicates
        $groupKeys = array_unique($groupKeys);
        $stringKeys = array_unique($stringKeys);

        // Add the translations to the database, if not existing.
        foreach ($groupKeys as $key) {
            // Split the group and item
            list($group, $item) = explode('.', $key, 2);
            $this->missingKey('', $group, $item);
        }

        foreach ($stringKeys as $key) {
            $group = self::JSON_GROUP;
            $item = $key;
            $this->missingKey('', $group, $item);
        }

        // Return the number of found translations
        return count($groupKeys + $stringKeys);
    }

    public function missingKey($namespace, $group, $key)
    {
        if (!in_array($group, config('SnayvikTranslation.exclude_groups'))) {
            SnvkTranslation::firstOrCreate([
                'locale' => config('app.locale'),
                'group'  => $group,
                'key'    => $key,
            ]);
        }
    }

}