<?php
namespace App\Http\Patterns;

use Lunar\Models\Order;
use App\Models\Logs;
use App\Models\OrderModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
// use Lunar\Models\Transaction;
use App\Models\Transaction;


class UpdateOrderStatus implements IOperations
{
    public  function doOperation(array $data)
    {
        $order = OrderModel::find($data['id']);
        // if ($order->status === config('lunar.payments.types.cash-in-hand.authorized') && $data['data']['status'] === 'payment-received') {
        //     $order->transactions()->create([
        //         'success' => true,
        //         'type' => 'capture',
        //         'driver' => 'offline',
        //         'amount' => $order->total->value,
        //         'reference' => 'offline',
        //         'status' => 'paid',
        //         'card_type' => 'offline',
        //         'meta' => [],
        //     ]);
        // }
        if ($data['data']['status'] === 'order_delivered') {
            $intentTrans = $order->transactions()->first();
            
            if ($intentTrans && $intentTrans->driver === 'coffline') {
                $intentTrans->update([
                    'success' => true,
                    'status' => 'paid',
                    'type' => 'capture'
                ]);
            }
        }
        $finData = ['status' => $data['data']['status']];
        if ($data['data']['status'] === 'order_processing') {
            $finData['placed_at'] = now();
        }
        if ($data['data']['status'] === 'order_canceled') {
            $transes = $order->transactions()->get();
            foreach ($transes as $key => $value) {
                if ($value->status === 'unpaid') {
                    $value->update([
                        'status' => 'canceled'
                    ]);
                }
            }
        }
        $order->update($finData);
        
        return $order;
    }

    public function audit()
    {
        Logs::createNewRecord('Update Status', 'Order');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN Update Order Status IN Order CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
