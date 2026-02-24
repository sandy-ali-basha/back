<?php


namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AddProductAttributesValuesData extends Data
{

    public array $ar;
    public array $kr;
    public array $en;
    public int $product_attributes_id;


    public function __construct(
        array  $ar,
        array  $kr,
        array  $en,
        int $product_attributes_id

    )
    {
        $this->ar = $ar;
        $this->kr = $kr;
        $this->en = $en;
        $this->product_attributes_id = $product_attributes_id;
    }



    public static function rules(): array
    {
        return [
            "ar.value" => "required",
            "en.value" => "required",
            "kr.value" => "required",
            "product_attributes_id" => "required",
        ];
    }
}
