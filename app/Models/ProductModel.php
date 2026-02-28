<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Lunar\Facades\DB;
use Lunar\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lunar\Models\ProductVariant;

class ProductModel extends Product
{
    protected $fillable = [
        'attribute_data',
        'product_type_id',
        'status',
        'brand_id',
        'points',
        'sku'
    ];
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('lunar.database.table_prefix') . 'products');

        if ($connection = config('lunar.database.connection', false)) {
            $this->setConnection($connection);
        }
    }

    public function images(): MorphMany|HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public function gallery(): MorphMany|HasMany
    {
        return $this->images()->where('type', '=', 'gallery');
    }
    public function slider(): MorphMany|HasMany
    {
        return $this->images()->where('type', '=', 'slider');
    }
    public static function create(array $attributes = [])
    {
        return static::query()->create($attributes);
    }

    public function ProductAttributesValues(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(ProductAttributesValues::class, 'attribute_product', 'product_model_id');
    }

    public static function filterByAttributes(?array $filters, ?int $brandId, ?int $productTypeId, ?float $minPrice, ?float $maxPrice)
    {
        $city_id = session()->get('city_id', 1);
        $productsIds = ProductModel::where(function ($query) use ($filters, $brandId, $productTypeId, $minPrice, $maxPrice,$city_id) {
//            if (!empty($filters)) {
//                $query->whereHas('ProductAttributesValues', function ($query) use ($filters) {
//                    foreach ($filters as $attributeId => $valueId) {
//                        $query->where('product_attributes_values.product_attributes_id', $attributeId)
//                            ->where('product_attributes_values_id', (int)$valueId);
//                    }
//                });
//            }
//sandy code multy filter
            // if (!empty($filters)) {
            //     foreach ($filters as $attributeId => $valueIds) {
            //         if (empty($valueIds)) {
            //             continue; // skip empty filters
            //         }

            //         $valueIds = (array) $valueIds;

            //         $query->whereHas('ProductAttributesValues', function ($q) use ($attributeId, $valueIds) {
            //             $q->where('product_attributes_values.product_attributes_id', $attributeId)
            //               ->whereIn('product_attributes_values_id', $valueIds);
            //         });
            //     }
            //}
if (!empty($filters)) {

    foreach ($filters as $filter) {

        $attributeId = $filter['attribute_id'] ?? null;
        $valueIds    = $filter['values'] ?? [];

        if (!$attributeId) {
            continue;
        }

        $query->whereHas('ProductAttributesValues', function ($q) use ($attributeId, $valueIds) {

            $q->where('product_attributes_values.product_attributes_id', $attributeId);

            if (!empty($valueIds)) {
                $q->whereIn('product_attributes_values_id', $valueIds);
            }

        });
    }
}

            if (!empty($brandId)) {
                $query->where('brand_id', $brandId);
            }

            if (!empty($productTypeId)) {
                $query->where('product_type_id', $productTypeId);
            }

            // if (!is_null($minPrice) || !is_null($maxPrice)) {
            // //     $query->whereRaw('EXISTS (
            // //     SELECT 1
            // //     FROM lunar_product_variants as variant
            // //     JOIN lunar_prices as prices ON prices.priceable_id = variant.id
            // //     WHERE variant.product_id = lunar_products.id
            // //     AND prices.price BETWEEN ? AND ?
            // //     LIMIT 1
            // // )', [$minPrice ?? 0, $maxPrice ?? PHP_INT_MAX]);
            //     $query->whereHas('variants', function($query) use ($minPrice, $maxPrice) {
            //         $query->whereHas('prices', function($q) use ($minPrice, $maxPrice) {
            //             $q->whereBetween('price', [$minPrice, $maxPrice]);
            //         });
            //     });
            // }
        });
        //->pluck('id');
        
        if (!is_null($minPrice) || !is_null($maxPrice)) {
           $productsIds = $productsIds->whereHas('variants', function ($query) use ($minPrice, $maxPrice) {
                $query
                    ->where('storage_qty', '>', 0)
                    ->whereHas('prices', function ($q) use ($minPrice, $maxPrice) {
                    $q->whereRaw('price/unit_quantity BETWEEN ? and ?', [$minPrice-1, $maxPrice+1]);
                });
            });
        }
        if ($city_id) {
           $productsIds = $productsIds->whereHas('variants', function ($query) use ($city_id) {
                $query
                    ->where('city_id', $city_id)
                  ;
            });
        }

        
        return $productsIds->get();

        // return self::whereIn('id',$productsIds)
        //     ->whereHas('cities', function ($query) use ($city_id) {
        //         $query->where('world_city_id', $city_id);
        //     })->get();
    }

    /**
     * Return the product type relation.
     *
     * @return BelongsTo
     */
    public function productType()
    {
        return $this->belongsTo(ProductTypeModel::class);
    }
//    public function getById($id)
//    {
//        $product =  $this->where('id', '=', $id)->first();
//
//        $productTranslation = $product->translations()->get();
//        $product->translations = $productTranslation;
//
//        return $product;
//    }
//sandy code
    public function getById($id)
    {
        $product = $this->with([
            'translations',
            'cities',            // product <-> cities pivot
            'variants',          // product -> variants
            'variants.currency', // variant -> currency
        ])->findOrFail($id);

        // 1) city ids (array of city model ids)
        $product->city_ids = $product->cities->pluck('id')->unique()->values();

        // 2) currency ids collected from variants
        $product->currency_ids = $product->variants
            ->map(function ($v) {
                // prefer related currency id if relation loaded, otherwise fallback to attribute
                return $v->currency->id ?? $v->currency_id ?? null;
            })
            ->filter()   // remove nulls
            ->unique()   // unique values
            ->values();  // reindex

        return $product;
    }

    /**
     * Return the product variants relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(
            City::class,
            'products_world_cities',
            'product_id',
            'world_city_id'
        );
    }
        public function cityPrices()
    {
        return $this->belongsToMany(City::class, 'city_product_prices')
                    ->withPivot('price')
                    ->withTimestamps();
    }
    public function regionPrices()
    {
        return $this->belongsToMany(Region::class, 'region_product_prices')
                    ->withPivot('price')
                    ->withTimestamps();
    }
}
