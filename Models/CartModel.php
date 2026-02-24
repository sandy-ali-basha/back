<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Lunar\Models\Cart;

class CartModel extends Cart
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('lunar.database.table_prefix').'cart');

        if ($connection = config('lunar.database.connection', false)) {
            $this->setConnection($connection);
        }
    }
    public static function create(array $attributes = [])
    {

        return static::query()->create($attributes);
    }

    public function getById($id)
    {
        $cart =  $this->where('id', '=', $id)->first();
        $cartTranslation = $cart->translations()->get();
        $cart->translations = $cartTranslation;

        return $cart;
    }
}
