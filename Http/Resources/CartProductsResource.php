<?php


namespace App\Http\Resources;

use App\Models\BrandModel;
use App\Models\ProductModel;
use App\Models\ProductTypeModel;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Lunar\Models\ProductVariant;
use  Lunar\DataTypes\Price;
class CartProductsResource extends JsonResource
{
    public function toArray($request)
    {

        $variant = ProductVariant::where('id',$this->purchasable_id)->first();

        $productModel = ProductModel::find( $variant->product_id);

        $quantity     = $variant->unit_quantity ?? 0;
        $stock = $this->purchasable()->first()->storage_qty - $this->quantity;
        $price        = $variant->getPrices()->first()->price->decimal(rounding: true);
        $data         = [
            'id' => $productModel->id,
            'variant_id' => $variant->id,
            'quantity' => $this->quantity,
            "name" => $productModel->translateAttribute('name', App::getLocale()),//
            //            "brand" => new BrandResource(BrandModel::where('id', $productModel->brand->id)->first()),//
            "price" => $price/$quantity,
            'stock' => $stock,
            'compare_price' => $this->meta->compare_price,
            'total_compare_price' => $this->meta->total_compare_price,
        ];
        $data['options'] = [];
        foreach ($variant->values as $value){
            $data['options'][] = [ // Use [] to append each variant as a new entry
                  'name'        => $value->name->{App::getLocale()??'en'},
                'id'        => $value->id,

            ];
        }
        if ($productModel->gallery()->get()) {
            $data['images'] = collect(new ImagesCollection($productModel->gallery()->get()));
        }

        if ($this->withTranslation) {
            $data['translations'] = $this->translations;
        }

        return $data;


    }
}
