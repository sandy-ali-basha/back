<?php
namespace App\Http\Patterns;

use App\Models\ProductAttributesValues;
use App\Models\Logs;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

class UpdateProductAttributesValues implements IOperations
{
    public  function doOperation(array $data)
    {
        $productAttributesValues = ProductAttributesValues::find($data['id']);

        $productAttributesValues->update($data['data']);
        $productAttributesValuesTranslation = $productAttributesValues->translations()->get();
        $productAttributesValues->translations = $productAttributesValuesTranslation;

        return $productAttributesValues;
    }

    public function audit()
    {
        Logs::createNewRecord('Update', 'product_attributes_values');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN Update product_attributes_values IN product_attributes_values CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
