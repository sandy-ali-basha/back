<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lunar\Models\Address;

class AddressModel extends Address
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('lunar.database.table_prefix').'addresses');

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
        $address =  $this->where('id', '=', $id)->first();

        return $address;
    }

    public function shipping_price()
    {
        $city = City::where('name', $this->city)->first();
    
        return $city ? $city->shipping_price : 0; // or null
    }
    
   
}
