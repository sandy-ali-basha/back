<?php

namespace App\Http\Patterns;

use Lunar\Models\Order;
use App\Models\Logs;
use App\Models\OrderModel;
use Illuminate\Support\Facades\Log;

class AddOrder implements IOperations
{
    public  function doOperation(array $data)
    {
        $order = OrderModel::create($data);

        return $order;
    }

    public function audit()
    {
        Logs::createNewRecord('Add', 'Order');
    }

    public function getErrorMessage()
    {
        \Illuminate\Support\Facades\Log::info('ERROR IN ADD Order IN Order CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
