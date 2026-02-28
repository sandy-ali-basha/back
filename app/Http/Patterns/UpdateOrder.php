<?php
namespace App\Http\Patterns;

use Lunar\Models\Order;
use App\Models\Logs;
use App\Models\OrderModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

class UpdateOrder implements IOperations
{
    public  function doOperation(array $data)
    {
        $order = OrderModel::find($data['id']);
        $order->update($data['data']);
        //$orderTranslation = $order->translations()->get();
        //$order->translations = $orderTranslation;

        return $order;
    }

    public function audit()
    {
        Logs::createNewRecord('Update', 'Order');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN Update Order IN Order CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
