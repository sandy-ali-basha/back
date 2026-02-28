<?php


namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AddAccordionData extends Data
{

    public array    $ar;
    public array    $kr;
    public array    $en;
    public int    $product_id;


    public function __construct(
        array $ar,
        array $kr,
        array $en,
        int    $product_id,

    )
    {
        $this->ar           = $ar;
        $this->kr           = $kr;
        $this->en           = $en;
        $this->product_id  = $product_id;
    }


    public static function rules(): array
    {
        return [
            'ar.title' => 'required',
            'kr.title' => 'required',
            'en.title' => 'required',
            'en.description' => 'required',
            'ar.description' => 'required',
            'kr.description' => 'required',
        ];
    }
}
