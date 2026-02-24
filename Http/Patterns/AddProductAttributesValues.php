<?php

namespace App\Http\Patterns;

use App\Models\ProductAttributesValues;
use App\Models\Logs;
use Illuminate\Support\Facades\Log;

class AddProductAttributesValues implements IOperations
{
    public  function doOperation(array $data)
    {

        $productAttributesValues = ProductAttributesValues::create($data);
        $productAttributesValuesTranslation = $productAttributesValues->translations()->get();
        $productAttributesValues->translations = $productAttributesValuesTranslation;

        return $productAttributesValues;
    }

    public function audit()
    {
        Logs::createNewRecord('Add', 'product_attributes_values');
    }

    public function getErrorMessage()
    {
        \Illuminate\Support\Facades\Log::info('ERROR IN ADD product_attributes_values IN product_attributes_values CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
