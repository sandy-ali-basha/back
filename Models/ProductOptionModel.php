<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductOption;

//use Lunar\Models\ProductOption;

class ProductOptionModel extends ProductOption
{
    use  Translatable;

    public array $translatedAttributes = ['name'];
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('lunar.database.table_prefix').'product_options');

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
        $productOption =  $this->where('id', '=', $id)->first();
        $productOptionTranslation = $productOption->translations()->get();
        $productOption->translations = $productOptionTranslation;

        return $productOption;
    }
}
