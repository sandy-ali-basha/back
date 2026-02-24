<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Lunar\Models\Order;
use Lunar\Models\OrderLine as ModelsOrderLine;
use Lunar\Base\Casts\Price;
use Lunar\Base\Casts\TaxBreakdown;

class OrderLine extends ModelsOrderLine
{
    protected $casts = [
        'unit_quantity' => 'integer',
        'quantity' => 'integer',
        'meta' => 'object',
        'tax_breakdown' => TaxBreakdown::class,
        'unit_price' => Price::class,
        'sub_total' => Price::class,
        'tax_total' => Price::class,
        'discount_total' => Price::class,
        'total' => Price::class,
        'description' => 'array',
    ];

    public function order() {
        return $this->belongsTo(OrderModel::class, "order_id", 'id');
    }
    
}
