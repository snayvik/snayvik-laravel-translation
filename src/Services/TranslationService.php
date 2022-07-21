<?php 

namespace Snayvik\Translation\Services;

use Snayvik\Translation\Models\SnvkTranslation;

class TranslationService
{
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
}