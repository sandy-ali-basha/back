<?php

namespace App\Services;

use App\Http\Patterns\AddProductAttributesValues;
use App\Http\Patterns\DeleteProductAttributesValues;
use App\Http\Patterns\UpdateProductAttributesValues;
use App\Models\ProductAttributesValues;
use App\Models\ProductModel;
use Illuminate\Database\Eloquent\Collection;


class ProductAttributesValuesService
{

    protected ProductAttributesValues $model;

    public function __construct(ProductAttributesValues $model)
    {
        $this->model = $model;
    }

    public function getAllProductAttributesValues($attId, $all=true): Collection
    {
        if ($all === 'true') {
            return ProductAttributesValues::where('product_attributes_id',$attId)->get();
        } else {
            // dd(
            //     ProductModel::with(['ProductAttributesValues'])->whereHas('ProductAttributesValues', function ($query) {
            //         $query->where('product_attributes_values.id', 18);
            //     })->get()->toArray()
            // );
            return ProductAttributesValues::getAllProductAttributesValues($attId, request()->header('city'));
        }
    }
    public function getAllProductAttributesValuesWebsite($attId): Collection
    {
        return ProductAttributesValues::getAllProductAttributesValues($attId);
    }

    public function createProductAttributesValues($data)
    {
        $value = new AddProductAttributesValues();

        return $value->doOperation($data->toArray());
    }

    public function getById($id)
    {
        return  $this->model->getProductAttributesValuesById($id);
    }

    public function updateProductAttributesValues($id, $data)
    {
        $value = new UpdateProductAttributesValues();

        return $value->doOperation(['id'=>$id,'data'=>$data->toArray()]);
    }

    public function deleteProductAttributesValues($id)
    {
        $value = new DeleteProductAttributesValues();

        return $value->doOperation(['id'=>$id]);
    }

}
