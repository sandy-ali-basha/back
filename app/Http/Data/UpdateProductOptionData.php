<?php


namespace App\Http\Data;

use Spatie\LaravelData\Data;

class UpdateProductOptionData extends Data
{

    public string $name;
    public array  $ar;
    public array  $kr;
    public array  $en;

    public function __construct(
        string $name,
        array  $ar,
        array  $kr,
        array  $en,
    )
    {
        $this->name = $name;
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
