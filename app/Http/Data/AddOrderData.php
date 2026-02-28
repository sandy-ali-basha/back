<?php


namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AddOrderData extends Data
{

    public int $address_id;


    public function __construct(
        int    $address_id,


    )
    {
        $this->address_id = $address_id;
    }


    public static function rules(): array
    {
        return [

        ];
    }
}
