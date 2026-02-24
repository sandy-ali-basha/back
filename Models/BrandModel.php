<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Lunar\Models\Brand;

class BrandModel extends Brand
{
    use  Translatable;

    public $translatedAttributes = ['name'];
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('lunar.database.table_prefix').'brands');

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
        $brand =  $this->where('id', '=', $id)->first();
        // $brandTranslation = $brand->translations()->get();
        // $brand->translations = $brandTranslation;

        return $brand;
    }

    /**
     * Return the product relationship.
     */
    public function products(): HasMany
    {
        return $this->hasMany(ProductModel::class, 'brand_id');
    }

    public function bproducts(): HasMany
    {
        return $this->hasMany(ProductModel::class, 'brand_id');
    }

        /**
     * Get all of the deployments for the project.
     */
    public function slides(): HasManyThrough
    {
        return $this->hasManyThrough(BrandSlide::class, BrandPages::class, 'brand_id', 'brand_page_id', 'id', 'id');
    }

    public function pages(): HasMany {
        return $this->hasMany(BrandPages::class, 'brand_id');
    }
}
