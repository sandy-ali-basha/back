<?php

namespace App\Services;


use App\Http\Patterns\AddDiscount;
use App\Http\Patterns\DeleteDiscount;
use App\Http\Patterns\UpdateDiscount;
use Illuminate\Database\Eloquent\Collection;
use Lunar\Models\Discount;

class DiscountService
{

    protected Discount $discount;

    public function __construct(Discount $discount)
    {
        $this->discount = $discount;
    }

    public function getAllDiscounts(): Collection
    {
        return Discount::all();
    }

    public function createDiscount($data)
    {
        
        $discount = new AddDiscount();

        return $discount->doOperation($data->toArray());
    }

    public function getById($id)
    {
        return  $this->discount->find($id);
    }
    public function getDiscountId($id)
    {
        return  Discount::find($id);
    }

    public function updateDiscount($id, $data)
    {
        $brand = new UpdateDiscount();

        return $brand->doOperation(['id'=>$id,'data'=>$data->toArray()]);
    }

    public function delete($id)
    {
        $brand = new DeleteDiscount();

        return $brand->doOperation(['id'=>$id]);
    }
}
