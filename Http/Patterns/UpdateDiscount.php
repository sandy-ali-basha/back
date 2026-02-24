<?php

namespace App\Http\Patterns;


use App\Models\Logs;
use Illuminate\Support\Facades\Log;
use Lunar\Models\Discount;

class UpdateDiscount implements IOperations
{
    public  function doOperation(array $data)
    {

        $discount = Discount::find($data['id']);
        $discount->update($data['data']);
        //$discountTranslations = $discount->translations()->get();
        //$discount->translations = $discountTranslations;

        return $discount;
    }

    public function audit()
    {
        Logs::createNewRecord('Update', 'discount');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN Update discount IN discount CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
