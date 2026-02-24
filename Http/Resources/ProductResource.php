<?php


namespace App\Http\Resources;

use App\Models\BrandModel;
use App\Models\City;
use App\Models\ProductModel;
use App\Models\ProductTypeModel;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {


        $data = [
            'id' => $this->id,
            'brand_id' => $this->brand_id,//
            'sku' => $this->sku,
            'status' => $this->status,
            'description' => $this->translateAttribute('description',App::getLocale()),
            "name"=>$this->translateAttribute('name',App::getLocale()),//
            "brand"=> $this->brand?new BrandResource(BrandModel::where('id',$this->brand->id)->first()):null,//
            "product_type"=>new ProductTypeResource(ProductTypeModel::where('id',$this->productType->id)->first()),
            "attributes"=>collect(new ProductAttributesValuesCollection(ProductModel::find($this->id)->ProductAttributesValues)),
            "points" => $this->points,
            "updated_at" => $this->updated_at,
            "length" => $this->attribute_data['length']??null,
            "weight" => $this->attribute_data['weight']??null,
            "density" => $this->attribute_data['density']??null,
            "width" => $this->attribute_data['width']??null,
            "division" => $this->attribute_data['division']??null,
            "height" => $this->attribute_data['height']??null
        ];
        $productModel = ProductModel::where('id',$this->id)->first();

        if ($productModel->gallery()->get()){
            $data['images'] = collect(new ImagesCollection($productModel->gallery()->get())) ;
        }

        if ($this->withTranslation) {
            $data['translations'] = $this->translations;
        }
    

        foreach ($this->variants()->get() as $variant) {

            $quantity = $variant->storage_qty ?? 0;
            $price= $variant->getPrices()->first();
            $result = DB::select("
    SELECT
        compare_price_start_date,
        compare_price_end_date
    FROM lunar_prices
    WHERE id = ?
", [$price?->id]);

            $data['variants'][] = [ // Use [] to append each variant as a new entry
                'id'        => $variant->id,
                'sku'        => $variant->sku,
                "price"=> (float) ($price?->price->decimal),
                "compare_price" => $price?->compare_price->decimal??null,
                "compare_price_start_date" => $result[0]->compare_price_start_date??null,
                "compare_price_end_date" => $result[0]->compare_price_end_date??null,
                'unit_quantity'        => $variant->unit_quantity,
                'tax_class_id'        => $variant->tax_class_id,
                'inventory'        => $variant->stock,
                'storage_qty'        => $variant->storage_qty,
                'reorder_point'   => $variant->reorder_point,
                'reorder_alert'   => $quantity<=$variant->reorder_point? true:false,
                'purchasable'=> $variant->purchasable,
                'quantity'   => $quantity,
                'options'   => $variant->getOptions(),

                'currency' => [
                    "id"=>$price?->price->currency->id,
                    "name"=>$price?->price->currency->name,
                    "code"=>$price?->price->currency->code
                ],
                'city'=>City::find($variant->city_id)->name??null
            ];
        }
        return $data;

    }
}
