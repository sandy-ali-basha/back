<?php

namespace App\Http\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Translations
{
    public static function getAllTranslations(Model $model){
        $data = [];
        foreach (Config::get('translatable.locales') as $locale){
            foreach ($model->attribute_data as $att){
                $data[]=[
                    "locale"=>$locale,
                ];
            }

        }
    }
}
