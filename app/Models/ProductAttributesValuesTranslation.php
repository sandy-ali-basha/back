<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributesValuesTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['value',];
    protected $table = 'product_attributes_values_translation';
}
