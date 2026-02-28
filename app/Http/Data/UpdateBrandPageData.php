<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class UpdateBrandPageData extends Data
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

        ];
    }
}
