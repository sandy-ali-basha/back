<?php

namespace App\Services;

use App\Http\Helpers\ValidateCartForOrderCreation as HelpersValidateCartForOrderCreation;
use App\Http\Patterns\AddOrder;
use App\Http\Patterns\DeleteOrder;
use App\Http\Patterns\UpdateOrder;
use App\Http\Patterns\UpdateOrderStatus;
use App\Models\OrderModel;
use Lunar\Actions\Carts\CreateOrder;
use Lunar\Models\Cart;
use Lunar\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Lunar\Validation\Cart\ValidateCartForOrderCreation;


class OrderService
{

    protected Order $model;
    protected OrderModel $orderModel;

    public function __construct(order $model, OrderModel $orderModel)
    {
        $this->model = $model;
        $this->orderModel = $orderModel;
    }

    public function getAllOrders(): Collection
    {
        return OrderModel::with('lines')->orderBy('created_at', 'DESC')->get();
    }

    public function createOrder($cart)
    {

        foreach (config('lunar.cart.validators.order_create', [
            HelpersValidateCartForOrderCreation::class,
        ]) as $action) {
            app($action)->using(
                cart: $cart,
            )->validate();
        }

        return app(
            config('lunar.cart.actions.order_create', CreateCustomOrder::class)
        )->execute($cart->refresh()->calculate())
         ->then(fn () => $cart->order->refresh());
    }

    public function getById($id)
    {
        return  $this->orderModel->getById($id);
    }

    public function updateOrder($id, $data)
    {
        $brand = new UpdateOrder();

        return $brand->doOperation(['id'=>$id,'data'=>$data->toArray()]);
    }

    public function deleteOrder($id)
    {
        $brand = new DeleteOrder();

        return $brand->doOperation(['id'=>$id]);
    }


    public function updateOrderStatus($id, $status)
    {
        $order = new UpdateOrderStatus();
           
        return $order->doOperation(['id'=>$id,'data' => ['status'=> $status]]);
    }

    public function findOrder($id) {
        return $this->model->find($id);
    }
}
