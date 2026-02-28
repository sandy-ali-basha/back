<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'city' => $this->city,
            'state' => $this->state,
            'postcode' => $this->postcode,
            'country' => '', //$this->country->name,
            'contact_mail' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'billing_default' => $this->billing_default,
            'customer_id' => $this->customer_id,
            'shipping_default' => $this->shipping_default,
            'line_one' => $this->line_one,
            'delivery_instructions' => $this->delivery_instructions,
            'shipping_price' => $this->shipping_price()
        ];

        return $data;

    }
}
