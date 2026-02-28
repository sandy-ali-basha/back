<?php

namespace App\Services;

use App\Http\Patterns\AddAccordion;
use App\Http\Patterns\DeleteAccordion;
use App\Http\Patterns\UpdateAccordion;
use App\Models\Accordion;
use Illuminate\Database\Eloquent\Collection;


class AccordionService
{

    protected Accordion $model;

    public function __construct(Accordion $model)
    {
        $this->model = $model;
    }

    public function getAllAccordions($product_id): Collection
    {
        return Accordion::where('product_id',$product_id)->get();
    }

    public function createAccordion($data)
    {
        $brand = new AddAccordion();
        return $brand->doOperation($data->toArray());
    }

    public function getById($id)
    {
        return  $this->model->getAccordionById($id);
    }

    public function updateAccordion($id, $data)
    {
        $brand = new UpdateAccordion();

        return $brand->doOperation(['id'=>$id,'data'=>$data->toArray()]);
    }

    public function deleteAccordion($id)
    {
        $brand = new DeleteAccordion();

        return $brand->doOperation(['id'=>$id]);
    }

}
