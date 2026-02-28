<?php
namespace App\Http\Patterns;;

use App\Models\ProductAttributes;
use App\Models\Logs;
use Illuminate\Support\Facades\Log;

class DeleteProductAttributes implements IOperations
{
    public function doOperation(array $data)
    {
        $product_attributes = ProductAttributes::find($data['id']);
        
        $product_attributes->deleteTranslations();
        $product_attributes->values()->delete();
        return $product_attributes->delete();
    }

    public function audit()
    {
        Logs::createNewRecord('delete', 'product_attributes');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN ADD NEW product_attributes us IN product_attributes CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
