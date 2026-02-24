<?php
namespace App\Http\Patterns;;

use App\Models\ProductAttributesValues;
use App\Models\Logs;
use Illuminate\Support\Facades\Log;

class DeleteProductAttributesValues implements IOperations
{
    public function doOperation(array $data)
    {
        $productAttributesValues = ProductAttributesValues::find($data['id']);
        $productAttributesValues->deleteTranslations();
        return $productAttributesValues->delete();
    }

    public function audit()
    {
        Logs::createNewRecord('delete', 'product_attributes_values');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN ADD NEW product_attributes_values us IN product_attributes_values CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
