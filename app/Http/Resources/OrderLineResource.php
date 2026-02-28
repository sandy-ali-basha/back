<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class OrderLineResource extends JsonResource
{
   

    public function __construct($resource, $payment=null) {
        parent::__construct($resource);
    }
    public function toArray($request)
    {

        $data = [
            "id" => $this->id,
            "order_id" => $this->order_id,
            "purchasable_type" => $this->purchasable_type,
            "purchasable_id" => $this->purchasable_id,
            "type" => $this->type,
            "description" => is_array($this->description) ? ($this->description[app()->getLocale()] ?? '') : $this->description,
            "option"=> $this->option,
            "identifier"=> $this->identifier,
            "unit_price"=> [ 'value' => $this->unit_price->decimal(true)],
            "unit_quantity"=> $this->unit_quantity,
            "quantity"=> $this->quantity,
            "sub_total"=> [ 'value' => $this->subtotal ? $this->subtotal->decimal(true): $this->total->decimal(true)],
            "discount_total"=> [ 'value' => $this->discount_total ? $this->discount_total->decimal(true) : $this->discount_total],
            "tax_breakdown"=> $this->tax_breakdown,
            "tax_total"=> $this->tax_total->decimal(true),
                "total"=> [ 'value' => $this->total->decimal(true)],
                "notes"=> $this->notes,
                "meta"=> $this->meta,
                "created_at"=> $this->created_at,
                "updated_at"=> $this->updated_at,
        ];
        

        return $data;
    }
}
