<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AddBrandPageData extends Data
{
    public int $brand_id;
    public array $ar;
    public array $kr;
    public array $en;

    public function __construct(
        array $ar,
        array $kr,
        array $en,
        int $brand_id

    )
    {

        $this->ar = $ar;
        $this->kr = $kr;
        $this->en = $en;
        $this->brand_id = $brand_id;

    }

    public static function rules(): array
    {
        return [
            'brand_id' => 'required',
            'ar.name' => 'required|string|max:255',
            'ar.text' => 'required',
            'kr.name' => 'required|string|max:255',
            'kr.text' => 'required',
            'en.name' => 'required|string|max:255',
            'en.text' => 'required',
        ];
    }
}
