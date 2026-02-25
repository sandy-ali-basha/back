<?php


namespace App\Http\Data;

use Spatie\LaravelData\Data;

class UpdateProductAttributesData extends Data
{

    public array $ar;
    public array $kr;
    public array $en;
    public ?bool $navActive;


    public function __construct(
        array  $ar,
        array  $kr,
        array  $en,
        ?bool $navActive = null,

    )
    {
        $this->ar = $ar;
        $this->kr = $kr;
        $this->en = $en;
        $this->navActive = $navActive;
    }


    public static function rules(): array
    {
        return [
            "navActive" => ['nullable', 'boolean'],
        ];
    }
}
