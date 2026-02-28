<?php


namespace App\Http\Patterns;
use App\Models\Logs;
use Illuminate\Support\Facades\Log;
use Lunar\Models\ProductOptionValue;

class DeleteProductOptionValue implements IOperations
{
    public function doOperation(array $data)
    {
        $productOptionValue = ProductOptionValue::find($data['id']);

        return $productOptionValue->delete();
    }

    public function audit()
    {
        Logs::createNewRecord('delete', 'ProductOption');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN Deleting NEW ProductOption value us IN ProductOption CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
