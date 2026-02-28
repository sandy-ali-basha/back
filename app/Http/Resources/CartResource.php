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
            // 'shipping_total' => $this->shippingSubTotal,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
            ];
        } else {
            $data = null;
        }

        return $data;

    }
}
