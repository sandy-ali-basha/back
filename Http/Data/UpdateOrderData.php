<?php


namespace App\Http\Data;

use Spatie\LaravelData\Data;

class UpdateOrderData extends Data
{

   public int $channel_id;
public status $status;
public string $reference;
public float $sub_total;


    public function __construct(
    int $channel_id,
status $status,
string $reference,
float $sub_total,

    )
    {

    }


    public static function rules(): array
    {
        return [

        ];
    }
}
