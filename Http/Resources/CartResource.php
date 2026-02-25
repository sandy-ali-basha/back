<?php


namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray($request)
    {
        if (isset($this->id)) {
            
            $subTotal = $this->subTotalDiscounted?$this->subTotalDiscounted->decimal(rounding: true):$this->sub_total;
      
            $data   = [
            'id'=>$this->id,
            'sub_total'=>$this->subTotal->decimal(rounding:true),
            'sub_total_after_discount' => $this->subTotalDiscounted->decimal(rounding: true),
            'sub_total_after_points'=>$this->total ? $this->total->decimal(rounding:true) : 0,
            'user_id'=>$this->user_id,
            'products'=>collect(new CartProductsCollection($this->lines)),
            'points_used' => $this->points_used,
            'discount_amount' => $this->discountTotal->decimal(rounding:true),
            'shipping_total' => ($this->shippingTotal?->decimal(rounding:true) ?? 0) + $this->resolveShippingPrice(),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
            ];
        } else {
            $data = null;
        }

        return $data;

    }
    private function resolveShippingPrice(): float
    {
        $shippingAddress = $this->shippingAddress ?? $this->addresses?->where('type', 'shipping')->first() ?? $this->addresses?->first();

        if (!$shippingAddress) {
            return 0.0;
        }

        $stateValue = is_string($shippingAddress->state ?? null) ? trim($shippingAddress->state) : $shippingAddress->state;
        $cityValue = is_string($shippingAddress->city ?? null) ? trim($shippingAddress->city) : $shippingAddress->city;

        if (is_numeric($stateValue)) {
            return (float) (\App\Models\City::find((int) $stateValue)?->shipping_price ?? 0);
        }

        if (is_numeric($cityValue)) {
            return (float) (\App\Models\City::find((int) $cityValue)?->shipping_price ?? 0);
        }

        $candidates = array_filter([
            $stateValue,
            is_string($stateValue) ? preg_replace('/^state\./', '', $stateValue) : null,
            $cityValue,
        ]);

        foreach ($candidates as $candidate) {
            $city = \App\Models\City::query()
                ->where('name', $candidate)
                ->orWhere('inv_name', $candidate)
                ->first();

            if ($city) {
                return (float) ($city->shipping_price ?? 0);
            }
        }

        return 0.0;
    }

}
