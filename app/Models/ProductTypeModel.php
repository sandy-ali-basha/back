<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Lunar\Models\Product;
use Lunar\Models\ProductType;

class ProductTypeModel extends ProductType
{
    use Translatable;

    public array $translatedAttributes = ['name'];
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('lunar.database.table_prefix').'product_types');

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
        $productType =  $this->where('id', '=', $id)->first();
        $productTypeTranslation =  $productType->translations()->get();
        $productType->translations =  $productTypeTranslation;

        return  $productType;
    }

        /**
     * Get the products relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(ProductModel::class, 'product_type_id');
    }
}
