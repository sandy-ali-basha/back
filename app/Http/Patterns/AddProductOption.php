<?php

namespace App\Http\Patterns;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Lunar\FieldTypes\Text;
use Lunar\FieldTypes\TranslatedText;
use Lunar\Models\ProductOption;
use App\Models\Logs;
use App\Models\ProductOptionModel;
use Illuminate\Support\Facades\Log;

class AddProductOption implements IOperations
{
    public  function doOperation(array $data)
    {
        /* @var ProductOption $productOption */
        $productOption = ProductOption::create([
            'name' => [
                'ar' => $data['ar']['name'],
                'kr' => $data['kr']['name'],
                'en' => $data['en']['name'],

            ]
        ]);

        $translations = [];
        foreach (Config::get('translatable.locales') as $locale){
            $translations[] = [
                "locale"=>$locale,
                "name"=>$productOption->translate('name',$locale),
            ];

        }
        $productOption->translations = $translations;
        return $productOption;
    }

    public function audit()
    {
        Logs::createNewRecord('Add', 'ProductOption');
    }

    public function getErrorMessage()
    {
        \Illuminate\Support\Facades\Log::info('ERROR IN ADD Product Option IN Product Options CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
