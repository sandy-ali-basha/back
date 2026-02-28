<?php


namespace App\Http\Data;

use Spatie\LaravelData\Data;

class UpdateProductAttributesValuesData extends Data
{


    public array $ar;
    public array $kr;
    public array $en;



    public function __construct(
        array  $ar,
        array  $kr,
        array  $en,

    )
    {
        $this->ar = $ar;
        $this->kr = $kr;
        $this->en = $en;

    }


    public static function rules(): array
    {
        return [

        ];
    }
}
