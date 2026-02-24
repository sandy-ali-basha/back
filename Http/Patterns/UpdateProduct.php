<?php
namespace App\Http\Patterns;

use App\Models\BrandModel;
use Illuminate\Support\Facades\Config;
use Lunar\FieldTypes\Number;
use Lunar\FieldTypes\Text;
use Lunar\FieldTypes\TranslatedText;
use Lunar\Models\Product;
use App\Models\Logs;
use App\Models\ProductModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use phpseclib3\Math\PrimeField\Integer;

class UpdateProduct implements IOperations
{
    public  function doOperation(array $data)
    {
        /* @var Product $product*/
        $product = ProductModel::find($data['id']);
        $data = $data['data'];
        
        $product->update([
            'product_type_id' => $data['product_type_id'],
            'brand_id' => $data['brand_id'],
            'attribute_data' => [
                'name' => new TranslatedText(collect([
                    'ar' => new Text($data['ar']['name']??$product->translateAttribute('name','ar')),
                    'kr' => new Text($data['kr']['name']??$product->translateAttribute('name','kr')),
                    'en' => new Text($data['en']['name']??$product->translateAttribute('name','en')),

                ])),
                'description' => new TranslatedText(collect([
                    'ar' => new Text($data['ar']['description']),
                    'kr' => new Text($data['kr']['description']),
                    'en' => new Text($data['en']['description']),
                ])),
                'tag' => new TranslatedText(collect([
                    'ar' => new Text($data['ar']['tag']??$product->translateAttribute('tag','ar')),
                    'kr' => new Text($data['kr']['tag']??$product->translateAttribute('tag','kr')),
                    'en' => new Text($data['en']['tag']??$product->translateAttribute('tag','en')),
                ])),
                'length' =>  new \Lunar\FieldTypes\Text($data['length']),
                'weight' =>  new \Lunar\FieldTypes\Text($data['weight']),
                'density' =>  new \Lunar\FieldTypes\Text($data['density']),
                'width' =>  new \Lunar\FieldTypes\Text($data['width']),
                'height' =>  new \Lunar\FieldTypes\Text($data['height']),
                'division' =>  new \Lunar\FieldTypes\Text($data['height']),

            ],
            "points" => $data['points']
        ]);
        
        $translations = [];
        foreach (Config::get('translatable.locales') as $locale){
            $translations[] = [
                "locale"=>$locale,
                "name"=>$product->translateAttribute('name',$locale),
                "description"=>$product->translateAttribute('description',$locale),
                "tag"=>$product->translateAttribute('tag',$locale)
            ];

        }

        $product->translations = $translations;
        return $product;
    }

    public function audit()
    {
        Logs::createNewRecord('Update', 'Product');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN Update Product IN Product CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
