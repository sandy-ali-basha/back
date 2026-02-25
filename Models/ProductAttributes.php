<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Lunar\Base\Traits\HasMedia;

use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;
use Lunar\Base\BaseModel;

class ProductAttributes extends BaseModel implements SpatieHasMedia
{
       public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable('product_attributes');

        if ($connection = config('lunar.database.connection', false)) {
            $this->setConnection($connection);
        }
    }
use HasFactory, SoftDeletes, Translatable, HasMedia;
    protected $fillable = ['title','status', 'nav_active'];
    protected $casts = [
        'status' => 'boolean',
        'nav_active' => 'boolean',
    ];
    public $translatedAttributes = ['title'];

    public function ProductAttributeValues(){
        return  $this->hasMany(ProductAttributesValues::class);
    }
    public function values(){
        return  $this->hasMany(ProductAttributesValues::class);
    }
    public function getProductAttributesById(int $id)
    {
        $productAttributes =  $this->where('id', '=', $id)->first();
        if ($productAttributes) {
            $productAttributesTranslation = $productAttributes->translations()->get();
            $productAttributes->translations = $productAttributesTranslation;
        }

        return $productAttributes;
    }
    
     public function getImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('product_attributes') 
            ?: asset('images/default.png');
    }
}
