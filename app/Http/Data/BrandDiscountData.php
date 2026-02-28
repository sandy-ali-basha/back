<?php
/**
 * Dwaa Test Lunar - ${FILE_NAME}
 *
 * Date: 24/06/13
 * Time: 7:23 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class BrandDiscountData extends Data
{
    public string $name;
    public ?string $min_price;
    public string $starts_at;
    public string $ends_at;
    public ?string $uses;
    public ?string $max_uses;
    public ?string $currency;

    public function __construct(
     string $name,
     string $starts_at,
     string $ends_at,
     ?string $uses,
     ?string $min_price,
     ?string $currency,

    )
    {

    }
//    public static function rules(): array
//    {
//        return [
//            'name' => 'required|string|max:255',
//            'email' => 'required|string|email|max:255|unique:users,email',
//            'password' => 'required|string|min:6|confirmed',
//        ];
//    }
}
