<?php

namespace App\Piplines;

use Closure;
use Lunar\DataTypes\Price;
use Lunar\Models\Cart;

class ReduceFromPoints
{
    /**
     * Called just before cart totals are calculated.
     *
     * @return void
     */
    public function handle(Cart $cart, Closure $next)
    {
        $settingService = app('\App\Services\SettingService');
        $point_price = $settingService->getByName('point_price');
        
        $point_price = floatval($point_price ? $point_price->value : 0);
        
        $cart->total->value = $cart->total->value - ($cart->points_used * $point_price);

        return $next($cart);
    }
}
