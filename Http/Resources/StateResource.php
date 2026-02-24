<?php


namespace App\Http\Resources;

use App\Models\City;
use Illuminate\Http\Resources\Json\JsonResource;
use Lunar\Models\ProductVariant;
use Lunar\Models\Product; // Required to reference the Product model

// NOTE: You will need to define App\Http\Resources\ProductCollection and
// App\Http\Resources\ProductResource to correctly handle the Lunar\Models\Product model.
use App\Http\Resources\ProductCollection; // Placeholder for the correct resource collection

class StateResource extends JsonResource
{
    public function toArray($request)
    {
        $city = City::with('currency')->where('id',$this->id)->first();

        // 1. Get the unique IDs of all products associated with the city.
        $distinctProductIds = ProductVariant::where('city_id', $city->id)
                                            ->distinct()
                                            ->pluck('product_id');

        // 2. Fetch all the unique Product models based on the collected IDs.
        // Assuming your Product model is \Lunar\Models\Product
        $products = Product::whereIn('id', $distinctProductIds)->get();

        // 3. The count is now simply the size of the resulting collection.
        $productsCount = $products->count();

        $data = [
            'id' => $this->id,
            "name" => trans("state.{$this->name}"),
            "value" => $this->name,
            'shipping_price' => $this->shipping_price,
            "products_count"=>$productsCount,
            "inv_name"=>$this->inv_name,
            "currency"=>[
                'name'=>$city->currency->name??"USD",
                'id'=>$city->currency->id??0,
                'code'=>$city->currency->code??"$"
            ],
            // FIX: Using ProductCollection, which must be defined to wrap Lunar\Models\Product
            "Products"=> new ProductCollection($products)
        ];


        return $data;

    }
}