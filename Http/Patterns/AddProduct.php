<?php

namespace App\Http\Patterns;

use App\Models\Logs;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Lunar\FieldTypes\Text;
use Lunar\FieldTypes\TranslatedText;
use App\Models\ProductModel as Product;
use phpseclib3\Math\PrimeField\Integer;

class AddProduct implements IOperations
{
    public function doOperation(array $data)
    {
        /* @var Product $product */
        //$city_id = $data['city_id'];
        $product= Product::create([
            'product_type_id' => $data['product_type_id'],
           'status' => "active",
            'brand_id' => $data['brand_id'],
              'sku' => $data['sku'],
            'attribute_data' => [
                'name' => new TranslatedText(collect([
                    'ar' => new Text($data['ar']['name']),
                    'kr' => new Text($data['kr']['name']),
                    'en' => new Text($data['en']['name']),
                ])),
                'description' => new TranslatedText(collect([
                    'ar' => new Text($data['ar']['description']),
                    'kr' => new Text($data['kr']['description']),
                    'en' => new Text($data['en']['description']),

                ])),
                'tag' => new TranslatedText(collect([
                    'ar' => new Text($data['ar']['tag']),
                    'kr' => new Text($data['kr']['tag']),
                    'en' => new Text($data['en']['tag']),
                ])),
                'weight' =>  new \Lunar\FieldTypes\Text($data['weight']),
                'density' =>  new \Lunar\FieldTypes\Text($data['density']),
                'length' =>  new \Lunar\FieldTypes\Text($data['length']),
                'width' =>  new \Lunar\FieldTypes\Text($data['width']),
                'height' =>  new \Lunar\FieldTypes\Text($data['height']),
                'division' =>  new \Lunar\FieldTypes\Text($data['height']),
            ],

        ]);


        $translations = [];
        foreach (Config::get('translatable.locales') as $locale) {
            $translations[] = [
                "locale" => $locale,
                "name" => $product->translateAttribute('name', $locale),
                "description" => $product->translateAttribute('description', $locale),
                "tag" => $product->translateAttribute('tag', $locale),
            ];

        }
        $product->translations = $translations;
        return $product;
    }

    public function audit()
    {
        Logs::createNewRecord('Add', 'Product');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN ADD Product IN Product CONTROLLER');
    }
    public function returnPage()
    {
        return redirect()->back();
    }
}
