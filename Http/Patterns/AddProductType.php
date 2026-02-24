<?php

namespace App\Http\Patterns;

use Lunar\Models\ProductType;
use App\Models\Logs;
use App\Models\ProductTypeModel;
use Illuminate\Support\Facades\Log;

class AddProductType implements IOperations
{
    public  function doOperation(array $data)
    {
        $productType = ProductTypeModel::create($data);

        $productTypeTranslation = $productType->translations()->get();
        $productType->translations = $productTypeTranslation;

        return $productType;
    }

    public function audit()
    {
        Logs::createNewRecord('Add', 'ProductType');
    }

    public function getErrorMessage()
    {
        \Illuminate\Support\Facades\Log::info('ERROR IN ADD ProductType IN ProductType CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
