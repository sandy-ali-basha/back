<?php

namespace App\Http\Patterns;
use Illuminate\Support\Facades\Config;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;


class AddProductOptionValues  implements IOperations
{

    public function __construct()
    {

    }

    public function doOperation(array $data)
    {
        /* @var ProductOption $productOption */
        $productOption = ProductOption::find($data['id']);
        $data = $data['data'];
        $value = new ProductOptionValue([
            'name' => [
                'ar' => $data['ar']['name'],
                'kr' => $data['kr']['name'],
                'en' => $data['en']['name'],

            ],
        ]);
        $productOption->values()->save($value);
        $translations = [];
        foreach (Config::get('translatable.locales') as $locale){
            $translations[] = [
                "locale"=>$locale,
                "name"=>$value->translate('name',$locale),
            ];

        }
        $value->translations = $translations;
        return $value;
    }

    public function audit()
    {
        // TODO: Implement audit() method.
    }

    public function getErrorMessage()
    {
        // TODO: Implement getErrorMessage() method.
    }

    public function returnPage()
    {
        // TODO: Implement returnPage() method.
    }
}
