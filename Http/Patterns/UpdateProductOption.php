<?php
namespace App\Http\Patterns;

use Illuminate\Support\Facades\Config;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use App\Models\Logs;
use App\Models\ProductOptionModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

class UpdateProductOption implements IOperations
{
    public  function doOperation(array $data)
    {
        /* @var ProductOption $productOption*/
        $productOption = ProductOption::find($data['id']);
        $data = $data['data'];
        $productOption->update([
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
        Logs::createNewRecord('Update', 'ProductOption');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN Update ProductOption IN ProductOption CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
