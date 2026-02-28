<?php
namespace App\Http\Patterns;

use Lunar\Models\ProductType;
use App\Models\Logs;
use App\Models\ProductTypeModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

class UpdateProductType implements IOperations
{
    public  function doOperation(array $data)
    {
        $productType = ProductTypeModel::find($data['id']);

        $productType->update($data['data']);
        $productTypeTranslation = $productType->translations()->get();
        $productType->translations = $productTypeTranslation;

        return $productType;
    }

    public function audit()
    {
        Logs::createNewRecord('Update', 'ProductType');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN Update ProductType IN ProductType CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
