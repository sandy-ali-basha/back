<?php
namespace App\Http\Patterns;;

use Lunar\Models\Cart;
use App\Models\Logs;
use App\Models\CartModel;
use Illuminate\Support\Facades\Log;

class DeleteCart implements IOperations
{
    public function doOperation(array $data)
    {
        $cart = Cart::find($data['id']);
        $cart->deleteTranslations();
        return $cart->delete();
    }

    public function audit()
    {
        Logs::createNewRecord('delete', 'Cart');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN ADD NEW Cart us IN Cart CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
