<?php

namespace App\Services;

use App\Http\Patterns\AddProductType;
use App\Http\Patterns\DeleteProductType;
use App\Http\Patterns\UpdateProductType;
use App\Models\ProductTypeModel;
use Lunar\Models\ProductType;
use Illuminate\Database\Eloquent\Collection;


class ProductTypeService
{

    protected ProductTypeModel $model;

    public function __construct(ProductTypeModel $model)
    {
        $this->model = $model;
    }

    public function getAllProductTypes(): Collection
    {
        return ProductTypeModel::all();
    }

    public function createProductType($data)
    {
        $brand = new AddProductType();

        return $brand->doOperation($data->toArray());
    }

    public function getById($id)
    {
        return  $this->model->getById($id);
    }

    public function updateProductType($id, $data)
    {
        $brand = new UpdateProductType();

        return $brand->doOperation(['id'=>$id,'data'=>$data->toArray()]);
    }

    public function deleteProductType($id)
    {
        $brand = new DeleteProductType();

        return $brand->doOperation(['id'=>$id]);
    }

}
