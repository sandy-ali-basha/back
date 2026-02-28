<?php


namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public $payment;

    public function __construct($resource, $payment=null) {
        parent::__construct($resource);
        if (is_numeric($payment)) {
            
            $pd = (new TransactionCollection($this->transactions))['transactions']??[];
            $this->payment = $pd;
        } else {
            $this->payment = $payment;
        }
    }
    public function toArray($request)
    {
        
        $data = [
            'id' => $this->id,
            'status' => $this->status,
            'reference' => $this->reference,
            'sub_total' => $this->sub_total->decimal(rounding: true),
            'total' => $this->total->decimal(true),
            'shipping_total' => $this->shipping_total->decimal(true),
            'sub_total_after_points' => $this->total->decimal(rounding: true),
            'points_used' => $this->points_used,
            'point_price' => $this->point_price,
            'payment_details' => $this->payment,
            'address' => $this->addresses,
            'customer' => $this->user?->customers,
            'created_at' => Carbon::createFromTimeString($this->created_at)->format('d-m-Y h:m A'),
            'updated_at' => Carbon::createFromTimeString($this->updated_at)->format('d-m-Y h:m A'),
            'transactions' => $this->transactions,
            'canCancel' => $this->canCancel(),
        ];
        if ($this->lines) {
            $data['lines'] = (new OrderLineCollection($this->lines))->resource->toArray();
            $data['points_added'] = 0;
            foreach ($this->lines as $key => $value) {
                if ($value->purchasable_type === 'Lunar\Models\ProductVariant') {
                    $data['points_added'] += $value->purchasable()->product()->first()->product()->first()->points * $value->quantity;
                }
            }
        }

        return $data;
    }
}
