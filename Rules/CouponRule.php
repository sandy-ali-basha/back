<?php

namespace App\Rules;

use App\Validators\CustomCouponValidator;
use Illuminate\Contracts\Validation\Rule;

class CouponRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return app(
            config('lunar.discounts.coupon_validator', CustomCouponValidator::class)
        )->validate($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is not valid or has been used too many times';
    }
}
