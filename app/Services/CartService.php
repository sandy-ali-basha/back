<?php

namespace App\Services;

use App\Http\Data\AddCartData;
use App\Http\Data\AddDiscountData;
use App\Http\Data\UseDiscountData;
use App\Http\Helpers\CodeGenerator;
use App\Http\Patterns\AddCart;
use App\Http\Patterns\DeleteCart;
use App\Http\Patterns\UpdateCart;
use Lunar\Drivers\SystemTaxDriver;
use Lunar\Models\Cart;
use Illuminate\Database\Eloquent\Collection;
use Lunar\Models\Currency;
use Lunar\Models\Discount;
use Lunar\Models\DiscountPurchasable;
use Lunar\Models\ProductVariant;
use phpDocumentor\Reflection\Exception;
use Illuminate\Support\Facades\Log;

class CartService
{

    protected Cart $model;

    public function __construct(Cart $model)
    {
        $this->model = $model;
    }

    public function getAllCarts(): Collection
    {
        return Cart::all();
    }

    public function createCart($data)
    {
        $cart = new AddCart();

        return $cart->doOperation($data->toArray());
    }

    public function getById($id)
    {
        return  Cart::find($id);
    }

    public function updateCart($id, $data)
    {
        $brand = new UpdateCart();

        return $brand->doOperation(['id' => $id, 'data' => $data->toArray()]);
    }

    public function deleteCart($id)
    {
        $brand = new DeleteCart();

        return $brand->doOperation(['id' => $id]);
    }


    public function addDiscount(UseDiscountData $data, Cart $cart)
    {
        /** @var  Discount $discount**/
        // return Discount::create([
        //     'name' => $data->name,
        //     'handle' => CodeGenerator::genererate(9),
        //     'type' => 'Lunar\DiscountTypes\Coupon',
        //     'data' => [
        //         'coupon' => $data->name,
        //         'min_prices' => [
        //             Currency::first()->name => $data->min_price
        //         ],
        //     ],
        //     'starts_at' => $data->starts_at,
        //     'ends_at' => $data->ends_at,
        //     'max_uses' => $data->max_uses,
        // ]);
        $cart->update(['coupon_code' => $data->coupon_code]);
        $cart->calculate();
        return $cart;
    }

    public function addProductsToCart(AddCartData $data, Cart $cart)
    {
        foreach ($data->products as $prd) {

            $prod = ProductVariant::where('id',$prd['variant_id'])->first();

            $line = $cart->lines()->where('purchasable_id', $prod->id)->first();
            if (!$prod){
                throw new Exception('prodcuts with variants not found');
            }
            if ($line) {
                $line->update(['quantity' => $line->quantity + $prd['qty']]);
            } else {
                $cart->lines()->create([
                    'cart_id' => $cart->id,
                    'purchasable_type' => ProductVariant::class,
                    'purchasable_id' => $prod->id,
                    'quantity' => $prd['qty']
                ]);
            }
        }

        $cart->refresh();
        $cart->calculate();

        return $cart;
    }

    public function getByUserId($user_id)
    {

        $cart =  $this->model->where("user_id", $user_id)
            ->orderBy('created_at', 'DESC')->first();
        if ($cart) {
            if ($cart->order_id) {
                $cart = null;
            } else {
                $cart->calculate();
            }
        }
        return $cart;
    }

    public function checkOrderCity($cart, $city = null)
    {
        foreach ($cart->lines as $key => $value) {
            if (!$city && $key === 0) {
                $city = $value->purchasable->product->cities[0]->id;
            } else {
                if ($value->purchasable->product->cities[0]->id !== $city) {
                    return false;
                }
            }
        }

        return true;
    }

    public function checkAddressCity($addresses, $city)
    {
        $exist = false;
        foreach ($addresses as $key => $value) {
           
            if ($value->city == $city) {
                $exist = true;
            }
        }
       
      
        return $exist;
    }
}
