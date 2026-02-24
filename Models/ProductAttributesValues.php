<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Lunar\Models\Product;

class ProductAttributesValues extends Model
{
    use HasFactory, SoftDeletes,Translatable;

    protected $fillable = ['value','product_attributes_id'];
    public $translatedAttributes = ['value',];

    public function ProductAttributes(){
        return  $this->belongsTo(ProductAttributes::class);
    }
    public function products()
    {
        return $this->belongsToMany(ProductModel::class, 'attribute_product','product_attributes_values_id');
    }
    public static function getAllProductAttributesValues($attId, $city=null)
    {
        if ($city !== null) {
            return self::where('product_attributes_id', $attId)
                       ->whereHas('products', function ($query) use ($city) {
                            $query->whereHas('cities', function($q) use ($city) {
                                $q->where('world_city_id', $city);
                            });
                       })
                       ->get();
        } else {
            return self::where('product_attributes_id', $attId)
                       ->has('products')
                       ->get();
        }
    }
    public function getProductAttributesValuesById(int $id)
    {
        $productAttributesValues =  $this->where('id', '=', $id)->first();
        $productAttributesValuesTranslation = $productAttributesValues->translations()->get();
        $productAttributesValues->translations = $productAttributesValuesTranslation;

        return $productAttributesValues;
    }
}
