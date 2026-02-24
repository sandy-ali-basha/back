<?php
/**
 * Dwaa Test Lunar - ${FILE_NAME}
 *
 * Date: 24/07/30
 * Time: 8:57 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Data;

use Illuminate\Validation\Rule;
use App\Rules\CouponRule;
use Spatie\LaravelData\Data;

class UseDiscountData extends Data
{

    public function __construct(
        public $cart_id,
        public string $coupon_code
    )
    {
      
    }

    public static function rules(): array
    {
        return [
            'cart_id' => ['required', Rule::exists('lunar_carts', 'id')->where('order_id', null)],
            'coupon_code' => ['required', 'string', Rule::exists('lunar_discounts', 'coupon'), new CouponRule],
        ];
    }
}
