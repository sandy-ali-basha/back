<?php
namespace App\Http\Patterns;

use Lunar\Models\Cart;
use App\Models\Logs;
use App\Models\CartModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

class UpdateCart implements IOperations
{
    public  function doOperation(array $data)
    {
        $cart = CartModel::find($data['id']);

        $cart->update($data['data']);
        $cartTranslation = $cart->translations()->get();
        $cart->translations = $cartTranslation;

        return $cart;
    }

    public function audit()
    {
        Logs::createNewRecord('Update', 'Cart');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN Update Cart IN Cart CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
