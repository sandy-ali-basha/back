<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    
    public function toArray($request)
    {
        
        $data = [
            'id' => $this->id,
            'parent_transaction_id' => $this->parent_transaction_id,
            'order_id' => $this->order_id,
            'success' => $this->success,
            'type' => $this->type,
            'driver' => $this->driver,
            'amount' => $this->amount->value,
            'reference' => $this->reference,
            'status' => $this->status,
            'notes' => $this->notes,
            'card_type' => $this->card_type,
            'last_four' => $this->last_four,
            'meta' => $this->meta,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'captured_at' => $this->captured_at,
            'currency    ' => $this->currency,
        ];
        
        return $data;
    }
}
