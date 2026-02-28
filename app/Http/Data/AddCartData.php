<?php


namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AddCartData extends Data
{


    public null|int $user_id;
    public null|string $coupon_code;
    public int $currency_id;
    public    array      $products;

    public function __construct(
        null|int $user_id,
        null|string $coupon_code,
        array $products,
        int $currency_id
    )
    {
        $this->user_id =$user_id;
        $this->coupon_code = $coupon_code;
        $this->products = $products;
        $this->currency_id = $currency_id;
    }


    public static function rules(): array
    {
        return [

        ];
    }
}
