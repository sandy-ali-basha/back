<?php

namespace App\Services;

use App\Http\Data\AddProductOptionValuesData;
use App\Http\Patterns\AddProductOption;
use App\Http\Patterns\AddProductOptionValues;
use App\Http\Patterns\DeleteProductOption;
use App\Http\Patterns\DeleteProductOptionValue;
use App\Http\Patterns\UpdateProductOption;
use App\Models\ProductOptionModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Lunar\Models\ProductOption;
use Illuminate\Database\Eloquent\Collection;
use Lunar\Models\ProductOptionValue;


class ProductOptionService
{

    protected ProductOption $model;

    public function __construct(ProductOption $model)
    {
        $this->model = $model;
    }

    public function getAllProductOptions(): Collection
    {

        return ProductOption::with('values')->get();
    }

    public function createProductOption($data): Model|Builder
    {
        $model = new AddProductOption();

        return $model->doOperation($data->toArray());
    }

    public function getById($id)
    {
        $product=   $this->model->where("id",$id)->first();
        $translations = [];
        foreach (Config::get('translatable.locales') as $locale){
            $translations[] = [
                "locale"=>$locale,
                "name"=>$product->translate('name',$locale),
            ];
            $product->translations = $translations;
        }
        return $product;
    }

    public function updateProductOption($id, $data)
    {
        $model = new UpdateProductOption();

        return $model->doOperation(['id'=>$id,'data'=>$data->toArray()]);
    }

    public function deleteProductOption($id)
    {
        $model = new DeleteProductOption();

        return $model->doOperation(['id'=>$id]);
    }

    public function createProductOptionValues(AddProductOptionValuesData $data, int $id)
    {
        $model = new AddProductOptionValues();

        return $model->doOperation(['id'=>$id,'data'=>$data->toArray()]);
    }

    public function getProductOptionValues($id)
    {
        return ProductOptionValue::where('product_option_id',$id)->get();
    }

    public function deleteProductOptionValue($id)
    {
        $model = new DeleteProductOptionValue();

        return $model->doOperation(['id'=>$id]);
    }

}
