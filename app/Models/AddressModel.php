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
        $stateValue = is_string($this->state) ? trim($this->state) : $this->state;
        $cityValue = is_string($this->city) ? trim($this->city) : $this->city;

        if (is_numeric($stateValue)) {
            return (float) (\App\Models\City::find((int) $stateValue)?->shipping_price ?? 0);
        }

        if (is_numeric($cityValue)) {
            return (float) (\App\Models\City::find((int) $cityValue)?->shipping_price ?? 0);
        }

        $candidates = array_filter([
            $stateValue,
            is_string($stateValue) ? preg_replace('/^state\./', '', $stateValue) : null,
            $cityValue,
        ]);

        foreach ($candidates as $candidate) {
            $city = \App\Models\City::query()
                ->where('name', $candidate)
                ->orWhere('inv_name', $candidate)
                ->first();

            if ($city) {
                return (float) ($city->shipping_price ?? 0);
            }
        }

        return 0.0;
    }
    
   
}
