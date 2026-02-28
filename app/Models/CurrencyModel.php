<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lunar\Models\Currency;
use Lunar\Models\Product;
use Nnjeim\World\Models\State as ModelsCity;

class CurrencyModel extends Currency
{


    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'currencies_cities')
                    ->using(CurrenciesCities::class)
                    ->withPivot(['currency_id', 'city_id']);
        // Add any custom fields from the pivot table here
        // ->withPivot('optional_field');
    }
    
}
