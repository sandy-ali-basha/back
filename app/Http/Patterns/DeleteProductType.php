<?php
namespace App\Http\Patterns;;

use Lunar\Models\ProductType;
use App\Models\Logs;
use App\Models\ProductTypeModel;
use Illuminate\Support\Facades\Log;

class DeleteProductType implements IOperations
{
    public function doOperation(array $data)
    {
        $productType = ProductTypeModel::find($data['id']);
        $productType->deleteTranslations();
        return $productType->delete();
    }

    public function audit()
    {
        Logs::createNewRecord('delete', 'ProductType');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN ADD NEW ProductType us IN ProductType CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
