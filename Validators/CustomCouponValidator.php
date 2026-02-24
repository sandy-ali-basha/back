<?php

namespace App\Validators;

use App\DiscountTypes\CustomAmountOff;
use Lunar\Models\Discount;
use Lunar\Base\Validation\CouponValidatorInterface;

class CustomCouponValidator implements CouponValidatorInterface
{
    public function validate(string $coupon): bool
    {
        return Discount::whereType(CustomAmountOff::class)
            ->active()
            ->where(function ($query) {
                $query->whereNull('max_uses')
                    ->orWhereRaw('uses < max_uses');
            })->where('coupon', '=', strtoupper($coupon))->exists();
    }
}
