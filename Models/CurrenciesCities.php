<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CurrenciesCities extends Pivot
{
    use HasFactory;

    // Explicitly set the pivot table name as provided earlier
    protected $table = "currencies_cities";

    // Since pivot tables typically don't have created_at/updated_at, you can disable timestamps
    public $timestamps = false;

    protected $fillable = [
        'currency_id',
        'city_id',
    ];
}
