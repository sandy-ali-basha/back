<?php

namespace App\Piplines;

use Closure;
use App\Managers\DiscountManager as Discounts;
use Lunar\Models\Cart;

final class ApplyDiscounts
{
    /**
     * Called just before cart totals are calculated.
     *
     * @return void
     */
    public function handle(Cart $cart, Closure $next)
    {
        foreach ($cart->lines as $key => $value) {
            $price = $value->purchasable->prices()->first()->compare_price->decimal(true);
            $value->update(['meta' => [
                'compare_price' => $price,
                'total_compare_price' => $price * $value->quantity 
            ]]);
        }
        $cart->discountBreakdown = collect([]);
        $discounts = new Discounts();
        $discounts->apply($cart);

        return $next($cart);
    }
}
