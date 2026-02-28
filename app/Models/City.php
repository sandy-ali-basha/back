<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lunar\Models\Currency;
use Lunar\Models\Product;
use Nnjeim\World\Models\State as ModelsCity;

class City extends ModelsCity
{
    use HasFactory;

    protected $table = 'world_states';
    protected $fillable = ['name', 'region_id','currency_id','inv_name','shipping_price'];


    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductModel::class, 
            'products_world_cities', 
            'world_city_id', 
            'product_id'
        );
    }
    
    public function productsPrice()
    {
        return $this->belongsToMany(Product::class, 'city_product_prices')
                    ->withPivot('price')
                    ->withTimestamps();
    }
    
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
        // Add any custom fields from the pivot table here
        // ->withPivot('optional_field');
    }
}
