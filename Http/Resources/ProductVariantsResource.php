<?php


namespace App\Http\Resources;

use App\Models\BrandModel;
use App\Models\City;
use App\Models\ProductModel;
use App\Models\ProductTypeModel;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Lunar\Models\ProductOption;

class ProductVariantsResource extends JsonResource
{
    public function toArray($request)
    {
        $quantity = $this->storage_qty ?? 0;
        $price= $this->getPrices()->first();
        $result = DB::select("
    SELECT
        compare_price_start_date,
        compare_price_end_date
    FROM lunar_prices
    WHERE id = ?
", [$price->id]);
        $data= [
            'id'        => $this->id,
            'sku'        => $this->sku,
            "price"=> (float) ($price->price->decimal),
            "compare_price" => $price->compare_price->decimal,
            "compare_price_start_date" => $result[0]->compare_price_start_date??null,
            "compare_price_end_date" => $result[0]->compare_price_end_date??null,
            'unit_quantity'        => $this->unit_quantity,
            'tax_class_id'        => $this->tax_class_id,
            'inventory'        => $this->stock,
            'storage_qty'        => $this->storage_qty,
            'purchasable'=> $this->purchasable,
            'quantity'   => $quantity,
            'reorder_point'   => $this->reorder_point??null,
            'reorder_alert'   => $quantity<=$this->reorder_point? true:false,
            'options'   => $this->getOptions(),

            'currency' => [
                "name"=>$price->price->currency->name,
                "code"=>$price->price->currency->code
            ],
            'city'=>City::find($this->city_id)->name
        ];



        return $data;

    }
}
