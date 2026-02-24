<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AddTermsData extends Data
{
    public array $kr;
    public array $en;
    public array $ar;

    public function __construct(
         array $kr,
     array $en,
     array $ar,

    )
    {
        $this->ar = $ar;
        $this->en = $en;
        $this->kr = $kr;
    }


    public static function rules(): array
    {
        return [

            'ar.name' => 'required',
            'kr.name' => 'required',
            'en.name' => 'required',
            'ar.text' => 'required',
            'kr.text' => 'required',
            'en.text' => 'required',
        ];
    }
}
