<?php
namespace App\Http\Patterns;;

use Lunar\Models\Order;
use App\Models\Logs;
use App\Models\OrderModel;
use Illuminate\Support\Facades\Log;

class DeleteOrder implements IOperations
{
    public function doOperation(array $data)
    {
        $order = Order::find($data['id']);
        $order->deleteTranslations();
        return $order->delete();
    }

    public function audit()
    {
        Logs::createNewRecord('delete', 'Order');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN ADD NEW Order us IN Order CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
