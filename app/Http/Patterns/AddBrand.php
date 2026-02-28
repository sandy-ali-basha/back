<?php

namespace App\Http\Patterns;

use App\Models\BrandModel;
use App\Models\Logs;


class AddBrand implements IOperations
{
    public  function doOperation(array $data)
    {
        $brand = BrandModel::create($data);

        $brandTranslation = $brand->translations()->get();
        $brand->translations = $brandTranslation;

        return $brand;
    }

    public function audit()
    {
        Logs::createNewRecord('Add', 'Brand');
    }

    public function getErrorMessage()
    {
        \Illuminate\Support\Facades\Log::info('ERROR IN ADD Brand IN brand CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
