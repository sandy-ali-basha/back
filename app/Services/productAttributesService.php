<?php

namespace App\Services;

use App\Http\Patterns\AddProductAttributes;
use App\Http\Patterns\DeleteProductAttributes;
use App\Http\Patterns\UpdateProductAttributes;
use App\Models\ProductAttributes;
use Illuminate\Database\Eloquent\Collection;


class productAttributesService
{

    protected ProductAttributes $model;

    public function __construct(ProductAttributes $model)
    {
        $this->model = $model;
    }

    public function getAllProductAttributes($all=true): Collection
    {
        if ($all) {
            return ProductAttributes::all();
        } else {
            return ProductAttributes::whereHas('ProductAttributeValues', function ($query) {
                $query->whereHas('products');
            })->get();
        }
    }

    public function createProductAttributes($data)
    {
        $brand = new AddProductAttributes();

        return $brand->doOperation($data->toArray());
    }

    public function getById($id)
    {
        return  $this->model->getProductAttributesById($id);
    }

    public function updateProductAttributes($id, $data)
    {
        $brand = new UpdateProductAttributes();

        return $brand->doOperation(['id'=>$id,'data'=>$data->toArray()]);
    }

    public function deleteProductAttributes($id)
    {
        $brand = new DeleteProductAttributes();

        return $brand->doOperation(['id'=>$id]);
    }

}
