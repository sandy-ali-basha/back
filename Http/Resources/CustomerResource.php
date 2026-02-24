<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Lunar\Models\Cart;
class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->users()->first();
        $cart = null;
        if($user){
           $cart= Cart::where('user_id',$user->id)->first();
        }
        $data= [
            'id'=>$this->id,
            'user_id'=> $user ? $user->id : null,
            'first_name'=>$this->first_name,
            'last_name'=>$this->last_name,
            'email'=> $user ? $user->email : '',
            'phone_number'=>$this->phone_number,
            'age'=>$this->age,
            'gender'=>$this->gender,
            'points' => $user && $user->rewardPoints ? $user->rewardPoints->points : 0,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "deleted_at" => $this->deleted_at,
        ];
        if ($this->token!=null){
            $data['token']=$this->token;
        }
            if ($cart!=null){
            $data['cart_id']= $cart->id;
        }
        return  $data;
    }
}
