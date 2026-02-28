<?php


namespace App\Http\Resources;

use App\Models\BrandModel;
use App\Models\ProductModel;
use App\Models\ProductTypeModel;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class BrandProductResource extends JsonResource
{
    public function toArray($request)
    {
        $quantity= $this->variants()->first()->unit_quantity;
        $price= $this->variants()->first()->getPrices()->first()->price->decimal(rounding: true);
        $data = [
            'id' => $this->id,
            'brand_id' => $this->brand_id,//
            'description' => $this->translateAttribute('description',App::getLocale()),
            "name"=>$this->translateAttribute('name',App::getLocale()),//
            "product_type"=>new ProductTypeResource(ProductTypeModel::where('id',$this->productType->id)->first()),
            "price"=>$price/$quantity,//
            "cities" => new StateCollection($this->cities),

        ];
        $productModel = ProductModel::where('id',$this->id)->first();
        if ($productModel->gallery()->get()){
            $data['image'] = $productModel->gallery()->first() ;
        }

        if ($this->withTranslation) {
            $data['translations'] = $this->translations;
        }

        return $data;

    }
}
