<?php


namespace App\Http\Data;

use Spatie\LaravelData\Data;

class UpdateProductTypeData extends Data
{

    public array|null    $en;
    public array|null    $ar;
    public array|null   $kr;


    public function __construct(
        array|null $en,
        array|null $ar,
        array|null $kr,

    )
    {
        $this->en           = $en;
        $this->ar           = $ar;
        $this->kr           = $kr;
    }


    public static function rules(): array
    {
        return [

        ];
    }
}
