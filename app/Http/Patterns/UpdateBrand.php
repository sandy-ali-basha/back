<?php

namespace App\Http\Patterns;

use App\Models\BrandModel;
use App\Models\Logs;
use Illuminate\Support\Facades\Log;
use Lunar\Models\Brand;

class UpdateBrand implements IOperations
{
    public  function doOperation(array $data)
    {

        $brand = BrandModel::find($data['id']);
        $brand->update($data['data']);
        $brandTranslation = $brand->translations()->get();
        $brand->translations = $brandTranslation;

        return $brand;
    }

    public function audit()
    {
        Logs::createNewRecord('Update', 'brand');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN Update brand IN brand brand CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
