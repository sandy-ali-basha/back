<?php

namespace App\Http\Patterns;

use App\Models\BrandModel;
use App\Models\Logs;
use Lunar\Models\Discount;

class AddDiscount implements IOperations
{
    public  function doOperation(array $data)
    {
        $discount = Discount::create($data);

        //$discountTranslations = $discount->translations()->get();
        //$discount->translations = $discountTranslations;

        return $discount;
    }

    public function audit()
    {
        Logs::createNewRecord('Add', 'Discount');
    }

    public function getErrorMessage()
    {
        \Illuminate\Support\Facades\Log::info('ERROR IN ADD Discount IN discount CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
