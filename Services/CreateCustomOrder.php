<?php

namespace App\Services;

use Lunar\Actions\AbstractAction;
use Lunar\Actions\Orders\GenerateOrderReference;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\DB;
use Lunar\Jobs\Orders\MarkAsNewCustomer;
use Lunar\Models\Cart;
use Lunar\Models\Currency;
//use Lunar\Models\Order;
use App\Models\OrderModel as Order;
use App\Models\Setting;

use Nnjeim\World\Models\State;
use Illuminate\Support\Facades\Log;

class CreateCustomOrder extends AbstractAction
{
    /**
     * Execute the action.
     *
     * @return void
     */
    public function execute(
        Cart $cart
    ) {
        $settingService = app('\App\Services\SettingService');
        $point = $settingService->getByName('point_price');
        $point_price = floatval($point ? $point->value : 0);
                $shipping_price = State::where('id',$cart->addresses()->first()->city)->first()->shipping_price;
        $freeLimit= Setting::find(79);
        if($freeLimit){
          if ($cart->total->decimal(true)>= $freeLimit->value){
            $shipping_price = 0.0;
        }
        }
         if ($cart->total->decimal(true)>50000.0){
            $shipping_price = 0.0;
        }
        return DB::transaction(function () use ($cart, $point_price, $shipping_price) {
            

            $order = Order::create([
                'user_id' => $cart->user_id,
                'channel_id' => $cart->channel_id,
                'status' => config('lunar.orders.draft_status'),
                'reference' => null,
                'customer_reference' => null,
                'sub_total' => $cart->subTotal->decimal(rounding: true),
                'total' => $cart->total->decimal(rounding: true)+($cart->shippingTotal?->decimal(rounding: true)+$shipping_price),
                'discount_total' => $cart->discountTotal?->decimal(rounding: true),
                'discount_breakdown' => [],
                'shipping_total' => $cart->shippingTotal?->decimal(rounding: true)+$shipping_price,
                'tax_breakdown' => $cart->taxBreakdown->map(function ($tax) {
                    return [
                        'description' => $tax['description'],
                        'identifier' => $tax['identifier'],
                        'percentage' => $tax['amounts']->min('percentage'),
                        'total' => $tax['total']->value,
                    ];
                })->values(),
                'tax_total' => $cart->taxTotal->value,
                'currency_code' => $cart->currency->code,
                'exchange_rate' => $cart->currency->exchange_rate,
                'compare_currency_code' => Currency::getDefault()?->code,
                'meta' => $cart->meta,
                'points_used' => $cart->points_used,
                'point_price' => $point_price,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $order->update([
                'reference' => app(GenerateOrderReference::class)->execute($order),
            ]);
            $points = 0;
            $orderLines = $cart->lines->map(function ($line) use (&$points) {
                
                $points += $line->purchasable()->product()->first()->product()->first()->points * $line->quantity;
                $line->purchasable->update([
                    'storage_qty' => $line->purchasable->storage_qty - $line->quantity
                ]);
                return [
                    'cart_line_id' => $line->id,
                    'purchasable_type' => $line->purchasable_type,
                    'purchasable_id' => $line->purchasable_id,
                    'type' => $line->purchasable->getType(),
                    'description' => $line->purchasable->getDescription(),
                    'option' => $line->purchasable->getOption(),
                    'identifier' => $line->purchasable->getIdentifier(),
                    'unit_price' => $line->unitPrice->decimal(rounding: true),
                    'unit_quantity' => $line->purchasable->getUnitQuantity(),
                    'quantity' => $line->quantity,
                    'sub_total' => $line->subTotal->decimal(rounding: true),
                    'discount_total' => $line->discountTotal?->decimal(rounding: true),
                    'tax_breakdown' => $line->taxBreakdown->amounts->map(function ($amount) {
                        return [
                            'description' => $amount->description,
                            'identifier' => $amount->identifier,
                            'percentage' => $amount->percentage,
                            'total' => $amount->price->decimal(rounding: true),
                        ];
                    })->values(),
                    'tax_total' => $line->taxAmount->decimal(rounding: true),
                    'total' => $line->total->decimal(rounding: true),
                    'notes' => null,
                    'meta' => $line->meta,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });
            if ($cart->user_id) {
                    $cart->user()->first()->increasePoints($points);
            }
            $addresses = collect();

            $cart->addresses->each(function ($address) use ($addresses, $shipping_price) {
                $data = $address->toArray();
                $data['city_shipping_price'] = $shipping_price;
                $addresses->push(
                    collect($data)->except('cart_id')
                );
            });

            // If we have a shipping address with a shipping option.
            if (($shippingAddress = $cart->shippingAddress) &&
                ($shippingOption = $cart->getShippingOption())
            ) {
                $orderLines->push([
                    'purchasable_type' => ShippingOption::class,
                    'purchasable_id' => 1,
                    'type' => 'shipping',
                    'description' => $shippingOption->getDescription(),
                    'option' => $shippingOption->getOption(),
                    'identifier' => $shippingOption->getIdentifier(),
                    'unit_price' => $shippingOption->price->value,
                    'unit_quantity' => $shippingOption->getUnitQuantity(),
                    'quantity' => 1,
                    'sub_total' => $shippingAddress->shippingSubTotal->decimal(rounding: true),
                    'discount_total' => $shippingAddress->shippingSubTotal->discountTotal?->decimal(rounding: true) ?: 0,
                    'tax_breakdown' => $shippingAddress->taxBreakdown->amounts->map(function ($amount) {
                        return [
                            'description' => $amount->description,
                            'identifier' => $amount->identifier,
                            'percentage' => $amount->percentage,
                            'total' => $amount->price->decimal(rounding: true),
                        ];
                    })->values(),
                    'tax_total' => $shippingAddress->shippingTaxTotal->decimal(rounding: true),
                    'total' => $shippingAddress->shippingTotal->decimal(rounding: true),
                    'notes' => null,
                    'meta' => [],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $cartLinesMappedToOrderLines = [];
            foreach ($orderLines as $orderLine) {
                $orderLineModel = $order->lines()->create(collect($orderLine)->except(['cart_line_id'])->all());

                if (isset($orderLine['cart_line_id'])) {
                    $cartLinesMappedToOrderLines[$orderLine['cart_line_id']] = $orderLineModel;
                }
            }

            $discountBreakdown = ($cart->discountBreakdown ?? collect())->map(function ($discount) use ($cartLinesMappedToOrderLines) {
                return (object) [
                    'discount_id' => $discount->discount->id,
                    'lines' => $discount->lines->map(function ($discountLine) use ($cartLinesMappedToOrderLines) {
                        return (object) [
                            'quantity' => $discountLine->quantity,
                            'line' => $cartLinesMappedToOrderLines[$discountLine->line->id],
                        ];
                    }),
                    'total' => $discount->price,
                ];
            })->values()->all();

            $order->update([
                'discount_breakdown' => $discountBreakdown,
            ]);

            $order->addresses()->createMany($addresses->toArray());

            $cart->order()->associate($order);

            $cart->discounts?->each(function ($discount) use ($cart) {
                $discount->markAsUsed($cart)->discount->save();
            });

            $cart->save();

            MarkAsNewCustomer::dispatch($order->id);

            return $this;
        });
    }
}

