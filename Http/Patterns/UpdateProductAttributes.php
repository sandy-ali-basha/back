<?php
namespace App\Http\Patterns;

use App\Models\ProductAttributes;
use App\Models\Logs;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

class UpdateProductAttributes implements IOperations
{
    public  function doOperation(array $data)
    {
        $product_attributes = ProductAttributes::find($data['id']);
        $product_attributes->update($data['data']);
        $product_attributesTranslation = $product_attributes->translations()->get();
        $product_attributes->translations = $product_attributesTranslation;
        return $product_attributes;
    }

    public function audit()
    {
        Logs::createNewRecord('Update', 'product_attributes');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN Update product_attributes IN product_attributes CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
