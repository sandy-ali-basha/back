<?php

namespace App\Http\Patterns;

use App\Models\Logs;
use Lunar\Models\Cart;
use Lunar\Models\Currency;

class AddCart implements IOperations
{
    public function doOperation(array $data)
    {
 
        return Cart::create([
            'currency_id' =>  $data['currency_id'],
            'channel_id' => 1,
            'user_id' => $data['user_id'],
            'coupon_code' => $data['coupon_code'],
        ]);
    }

    public function audit()
    {
        Logs::createNewRecord('Add', 'Cart');
    }

    public function getErrorMessage()
    {
        \Illuminate\Support\Facades\Log::info('ERROR IN ADD Cart IN Cart CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
