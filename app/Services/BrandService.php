<?php

namespace App\Services;

use App\Http\Data\AddBrandPageData;
use App\Http\Patterns\AddBrand;
use App\Http\Patterns\AddBrandDiscount;
use App\Http\Patterns\AddBrandPage;
use App\Http\Patterns\DeleteBrand;
use App\Http\Patterns\UpdateBrand;
use App\Models\BrandModel;
use Illuminate\Database\Eloquent\Collection;
// use Lunar\Models\Brand;
use App\Models\BrandModel as Brand;


class BrandService
{

    protected BrandModel $brand;

    public function __construct(BrandModel $brand)
    {
        $this->brand = $brand;
    }

    public function getAllBrands(): Collection
    {
        return BrandModel::all();
    }

    public function createBrand($data)
    {
        $brand = new AddBrand();

        return $brand->doOperation($data->toArray());
    }

    public function getById($id)
    {
        return  $this->brand->getById($id);
    }
    public function getBrandId($id)
    {
        return  Brand::find($id);
    }

    public function updateBrand($id, $data)
    {
        $brand = new UpdateBrand();

        return $brand->doOperation(['id'=>$id,'data'=>$data->toArray()]);
    }

    public function deleteBrand($id)
    {
        $brand = new DeleteBrand();

        return $brand->doOperation(['id'=>$id]);
    }

    public function addDiscountToBrand($brandId,$brandDiscountData)
    {
        $brand = new AddBrandDiscount();
        return $brand->doOperation(['brandId'=>$brandId,'brandDiscountData'=>$brandDiscountData]);
    }

    public function getProduct(int $id)
    {
        $city_id = session()->get('city_id', 1);
        $brandModel = Brand::find($id);
        return $brandModel->products()->with('variants')->whereHas('cities', function ($query) use ($city_id) {
            $query->where('world_city_id', $city_id);
        })->take(5)->get();
    }

    public function addBrandPage(AddBrandPageData $data)
    {
        $brand = new AddBrandPage();
        return $brand->doOperation($data->toArray());
    }
}
